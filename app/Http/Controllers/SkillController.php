<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    private array $categories = [
        'Academic',
        'Programming',
        'Technical',
        'Creative',
        'Sports',
        'Language',
        'Business',
        'Music',
        'Public Speaking',
        'Design',
        'Other',
    ];

    /**
     * Validate and normalize incoming skill payload data.
     */
    private function skillPayload(Request $request): array
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => [
                'required',
                Rule::in($this->categories),
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
            'exchange_skill_needed' => 'nullable|string|max:255|required_if:skill_type,exchange',
            'collaboration_goal' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in([Skill::STATUS_ACTIVE, Skill::STATUS_INACTIVE])],
        ]);

        $validated['skill_type'] = Skill::normalizedType($validated['skill_type']);
        $validated['status'] = $validated['status'] ?? Skill::STATUS_ACTIVE;

        if ($validated['skill_type'] !== Skill::TYPE_EXCHANGE) {
            $validated['exchange_skill_needed'] = null;
        }

        return $validated;
    }

    /**
     * Check whether the same exchange pair already exists.
     */
    private function isDuplicateExchange(array $validated, ?int $ignoreId = null): bool
    {
        if ($validated['skill_type'] !== Skill::TYPE_EXCHANGE) {
            return false;
        }

        $query = Skill::query()
            ->where('user_id', Auth::id())
            ->where('skill_type', Skill::TYPE_EXCHANGE)
            ->whereRaw('LOWER(TRIM(skill_name)) = ?', [Str::lower(trim((string) $validated['skill_name']))])
            ->whereRaw('LOWER(TRIM(exchange_skill_needed)) = ?', [Str::lower(trim((string) ($validated['exchange_skill_needed'] ?? '')))]);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    /**
     * Display the main page data for this feature.
     */
    public function index()
    {
        $skills = Skill::with('user.profile')
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->where(function ($query) {
                $query->where('status', Skill::STATUS_ACTIVE)
                    ->orWhere('user_id', Auth::id());
            })
            ->latest()
            ->get();

        return view('skills.index', compact('skills'));
    }

    /**
     * Validate input and persist a new record.
     */
    public function store(Request $request)
    {
        $validated = $this->skillPayload($request);

        if ($this->isDuplicateExchange($validated)) {
            return back()
                ->withInput()
                ->withErrors([
                    'skill_name' => 'You already created this exchange skill with the same offered and wanted skill.',
                ]);
        }

        Skill::create([
            'user_id' => Auth::id(),
            'skill_name' => $validated['skill_name'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'skill_type' => $validated['skill_type'],
            'skill_level' => $validated['skill_level'],
            'availability' => $validated['availability'] ?? null,
            'exchange_skill_needed' => $validated['exchange_skill_needed'] ?? null,
            'collaboration_goal' => $validated['collaboration_goal'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('skills.index')
            ->with('success', 'Skill shared successfully.');
    }

    /**
     * Show the form used to edit an existing record.
     */
    public function edit(Skill $skill)
    {
        abort_unless((int) $skill->user_id === (int) Auth::id(), 403);

        return view('skills.edit', compact('skill'));
    }

    /**
     * Validate input and persist updates to an existing record.
     */
    public function update(Request $request, Skill $skill)
    {
        abort_unless((int) $skill->user_id === (int) Auth::id(), 403);

        $validated = $this->skillPayload($request);

        if ($this->isDuplicateExchange($validated, $skill->id)) {
            return back()
                ->withInput()
                ->withErrors([
                    'skill_name' => 'You already have this exchange pair in another skill.',
                ]);
        }

        $skill->update($validated);

        return redirect()->route('skills.index')
            ->with('success', 'Skill updated successfully.');
    }

    /**
     * Toggle the record status between active and inactive.
     */
    public function toggleStatus(Skill $skill)
    {
        abort_unless((int) $skill->user_id === (int) Auth::id(), 403);

        $skill->update([
            'status' => $skill->isActive() ? Skill::STATUS_INACTIVE : Skill::STATUS_ACTIVE,
        ]);

        return redirect()->route('skills.index')
            ->with('success', 'Skill status updated.');
    }

    /**
     * Delete the specified record from storage.
     */
    public function destroy(Skill $skill)
    {
        abort_unless((int) $skill->user_id === (int) Auth::id(), 403);

        $skill->delete();

        return redirect()->route('skills.index')
            ->with('success', 'Skill deleted successfully.');
    }

    /**
     * Build and return ranked matching results.
     */
    public function matches(Request $request, Skill $skill)
    {
        abort_unless((int) $skill->user_id === (int) Auth::id(), 403);

        if (!$skill->isActive()) {
            return redirect()->route('skills.index')
                ->with('error', 'Inactive skills cannot be used for public matching.');
        }

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
            ->where('status', Skill::STATUS_ACTIVE)
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