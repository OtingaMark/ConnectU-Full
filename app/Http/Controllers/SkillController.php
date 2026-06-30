<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    private function autoExchangeDescription(string $offeredSkillName): string
    {
        return 'Added automatically because user wants this skill in exchange for ' . $offeredSkillName . '.';
    }

    private function syncExchangeWantedSkill(Skill $exchangeSkill): void
    {
        if (
            $exchangeSkill->normalized_type !== Skill::TYPE_EXCHANGE ||
            empty(trim((string) $exchangeSkill->exchange_skill_needed))
        ) {
            return;
        }

        $neededSkillName = trim((string) $exchangeSkill->exchange_skill_needed);
        $normalizedNeeded = Str::lower($neededSkillName);

        $linkedAutoSkill = Skill::query()
            ->where('user_id', $exchangeSkill->user_id)
            ->where('exchange_parent_skill_id', $exchangeSkill->id)
            ->where('auto_created_from_exchange', true)
            ->first();

        $existingWantedSkill = Skill::query()
            ->where('user_id', $exchangeSkill->user_id)
            ->where('skill_type', Skill::TYPE_WANT_TO_LEARN)
            ->where('id', '!=', $exchangeSkill->id)
            ->whereRaw('LOWER(TRIM(skill_name)) = ?', [$normalizedNeeded])
            ->first();

        $targetSkill = $existingWantedSkill ?? $linkedAutoSkill;

        if (!$targetSkill) {
            Skill::create([
                'user_id' => $exchangeSkill->user_id,
                'skill_name' => $neededSkillName,
                'description' => $this->autoExchangeDescription($exchangeSkill->skill_name),
                'category' => $exchangeSkill->category,
                'skill_type' => Skill::TYPE_WANT_TO_LEARN,
                'skill_level' => $exchangeSkill->skill_level,
                'availability' => $exchangeSkill->availability,
                'auto_created_from_exchange' => true,
                'exchange_parent_skill_id' => $exchangeSkill->id,
            ]);

            return;
        }

        $updates = [
            'skill_name' => $neededSkillName,
            'category' => $exchangeSkill->category,
            'skill_type' => Skill::TYPE_WANT_TO_LEARN,
            'skill_level' => $exchangeSkill->skill_level,
            'availability' => $exchangeSkill->availability,
        ];

        if ($targetSkill->auto_created_from_exchange) {
            $updates['description'] = $this->autoExchangeDescription($exchangeSkill->skill_name);
            $updates['exchange_parent_skill_id'] = $exchangeSkill->id;
        }

        $targetSkill->update($updates);

        if (
            $linkedAutoSkill &&
            $existingWantedSkill &&
            (int) $linkedAutoSkill->id !== (int) $existingWantedSkill->id
        ) {
            $linkedAutoSkill->delete();
        }
    }

    private function cleanupLinkedAutoExchangeSkill(Skill $exchangeSkill): void
    {
        Skill::query()
            ->where('user_id', $exchangeSkill->user_id)
            ->where('exchange_parent_skill_id', $exchangeSkill->id)
            ->where('auto_created_from_exchange', true)
            ->delete();
    }

    private function skillPayload(Request $request): array
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => [
                'required',
                Rule::in(['Academic', 'Programming', 'Sports', 'Music', 'Public Speaking', 'Design', 'Other']),
            ],
            'skill_type' => [
                'required',
                Rule::in([
                    Skill::TYPE_CAN_TEACH,
                    Skill::TYPE_WANT_TO_LEARN,
                    Skill::TYPE_EXCHANGE,
                    Skill::TYPE_TEAMWORK,
                    'teach',
                    'learn',
                ]),
            ],
            'skill_level' => [
                'required',
                Rule::in(['Beginner', 'Intermediate', 'Advanced']),
            ],
            'availability' => 'nullable|string|max:255',
            'exchange_skill_needed' => 'nullable|string|max:255',
            'collaboration_goal' => 'nullable|string|max:255',
        ]);

        $validated['skill_type'] = Skill::normalizedType($validated['skill_type']);

        return $validated;
    }

    public function index()
    {
        $skills = Skill::with('user')
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->latest()
            ->get();

        return view('skills.index', compact('skills'));
    }

    public function store(Request $request)
    {
        $validated = $this->skillPayload($request);

        DB::transaction(function () use ($validated) {
            $skill = Skill::create([
                'user_id' => Auth::id(),
                'skill_name' => $validated['skill_name'],
                'description' => $validated['description'] ?? null,
                'category' => $validated['category'],
                'skill_type' => $validated['skill_type'],
                'skill_level' => $validated['skill_level'],
                'availability' => $validated['availability'] ?? null,
                'exchange_skill_needed' => $validated['exchange_skill_needed'] ?? null,
                'collaboration_goal' => $validated['collaboration_goal'] ?? null,
            ]);

            $this->syncExchangeWantedSkill($skill);
        });

        return redirect()->route('skills.index')
            ->with('success', 'Skill shared successfully.');
    }

    public function edit(Skill $skill)
    {
        abort_unless((int) $skill->user_id === (int) Auth::id(), 403);

        return view('skills.edit', compact('skill'));
    }

    public function update(Request $request, Skill $skill)
    {
        abort_unless((int) $skill->user_id === (int) Auth::id(), 403);

        $validated = $this->skillPayload($request);

        DB::transaction(function () use ($skill, $validated) {
            $skill->update($validated);
            $skill->refresh();

            if (
                $skill->normalized_type === Skill::TYPE_EXCHANGE &&
                !empty(trim((string) $skill->exchange_skill_needed))
            ) {
                $this->syncExchangeWantedSkill($skill);

                return;
            }

            $this->cleanupLinkedAutoExchangeSkill($skill);
        });

        return redirect()->route('skills.index')
            ->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        abort_unless((int) $skill->user_id === (int) Auth::id(), 403);

        DB::transaction(function () use ($skill) {
            $this->cleanupLinkedAutoExchangeSkill($skill);
            $skill->delete();
        });

        return redirect()->route('skills.index')
            ->with('success', 'Skill deleted successfully.');
    }

    public function matches(Request $request, Skill $skill)
    {
        abort_unless((int) $skill->user_id === (int) Auth::id(), 403);

        $sourceType = $skill->normalized_type;

        $compatibleTypes = match ($sourceType) {
            Skill::TYPE_CAN_TEACH => [Skill::TYPE_WANT_TO_LEARN],
            Skill::TYPE_WANT_TO_LEARN => [Skill::TYPE_CAN_TEACH],
            Skill::TYPE_EXCHANGE => [Skill::TYPE_EXCHANGE, Skill::TYPE_CAN_TEACH, Skill::TYPE_WANT_TO_LEARN],
            Skill::TYPE_TEAMWORK => [Skill::TYPE_TEAMWORK, Skill::TYPE_EXCHANGE],
            default => [Skill::TYPE_WANT_TO_LEARN],
        };

        $filters = [
            'search' => trim((string) $request->query('search', $skill->skill_name)),
            'category' => trim((string) $request->query('category', $skill->category)),
            'skill_type' => trim((string) $request->query('skill_type', 'all')),
            'skill_level' => trim((string) $request->query('skill_level', 'all')),
        ];

        $query = Skill::with('user')
            ->where('id', '!=', $skill->id)
            ->where('user_id', '!=', Auth::id())
            ->whereHas('user', fn ($builder) => $builder->active());

        if ($filters['category'] !== '') {
            $query->where('category', $filters['category']);
        }

        if ($filters['skill_level'] !== 'all') {
            $query->where('skill_level', $filters['skill_level']);
        }

        if ($filters['skill_type'] !== 'all') {
            $query->where('skill_type', Skill::normalizedType($filters['skill_type']));
        } else {
            $query->whereIn('skill_type', $compatibleTypes);
        }

        $tokens = collect(preg_split('/[^a-z0-9]+/i', Str::lower($filters['search'])))
            ->filter(fn ($value) => strlen($value) >= 2)
            ->values();

        if ($tokens->isNotEmpty()) {
            $query->where(function ($builder) use ($tokens, $filters) {
                $builder->orWhere('skill_name', 'like', '%' . $filters['search'] . '%');
                $builder->orWhere('description', 'like', '%' . $filters['search'] . '%');

                foreach ($tokens as $token) {
                    $builder->orWhere('skill_name', 'like', '%' . $token . '%')
                        ->orWhere('description', 'like', '%' . $token . '%')
                        ->orWhere('exchange_skill_needed', 'like', '%' . $token . '%')
                        ->orWhere('collaboration_goal', 'like', '%' . $token . '%');
                }
            });
        }

        $matches = $query->get()->map(function (Skill $candidate) use ($skill, $tokens, $sourceType, $compatibleTypes) {
            $score = 0;

            if (Str::lower($candidate->skill_name) === Str::lower($skill->skill_name)) {
                $score += 45;
            }

            if ($candidate->category === $skill->category) {
                $score += 15;
            }

            if ($candidate->skill_level === $skill->skill_level) {
                $score += 10;
            }

            if (in_array($candidate->normalized_type, $compatibleTypes, true)) {
                $score += 15;
            }

            foreach ($tokens as $token) {
                if (Str::contains(Str::lower((string) $candidate->skill_name), $token)) {
                    $score += 8;
                }

                if (Str::contains(Str::lower((string) $candidate->description), $token)) {
                    $score += 4;
                }
            }

            if (
                $sourceType === Skill::TYPE_EXCHANGE &&
                !empty($skill->exchange_skill_needed) &&
                Str::contains(
                    Str::lower((string) $candidate->skill_name . ' ' . $candidate->description),
                    Str::lower((string) $skill->exchange_skill_needed)
                )
            ) {
                $score += 20;
            }

            $candidate->match_score = $score;

            return $candidate;
        })->sortByDesc('match_score')->values();

        return view('skills.matches', [
            'sourceSkill' => $skill,
            'matches' => $matches,
            'filters' => $filters,
            'compatibleTypes' => $compatibleTypes,
        ]);
    }
}