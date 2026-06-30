<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSkillSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            ['name' => 'Java', 'category' => 'Technical'],
            ['name' => 'Python', 'category' => 'Technical'],
            ['name' => 'Laravel', 'category' => 'Technical'],
            ['name' => 'PHP', 'category' => 'Technical'],
            ['name' => 'MySQL', 'category' => 'Technical'],
            ['name' => 'UI/UX', 'category' => 'Creative'],
            ['name' => 'Data Analysis', 'category' => 'Academic'],
            ['name' => 'Cyber Security', 'category' => 'Technical'],
            ['name' => 'Machine Learning', 'category' => 'Academic'],
            ['name' => 'Public Speaking', 'category' => 'Business'],
            ['name' => 'German', 'category' => 'Language'],
            ['name' => 'French', 'category' => 'Language'],
            ['name' => 'Graphic Design', 'category' => 'Creative'],
            ['name' => 'Photography', 'category' => 'Creative'],
            ['name' => 'Football', 'category' => 'Sports'],
            ['name' => 'Basketball', 'category' => 'Sports'],
            ['name' => 'Chess', 'category' => 'Sports'],
            ['name' => 'Content Creation', 'category' => 'Creative'],
            ['name' => 'Video Editing', 'category' => 'Creative'],
            ['name' => 'Accounting', 'category' => 'Business'],
            ['name' => 'Mathematics', 'category' => 'Academic'],
        ];

        $students = User::query()->where('role', 'student')->get();

        foreach ($students as $student) {
            $count = rand(2, 6);
            $picked = collect($catalog)->shuffle()->take($count)->values();

            foreach ($picked as $item) {
                $typeRoll = rand(1, 100);
                $skillType = match (true) {
                    $typeRoll <= 40 => Skill::TYPE_CAN_TEACH,
                    $typeRoll <= 70 => Skill::TYPE_WANT_TO_LEARN,
                    $typeRoll <= 90 => Skill::TYPE_EXCHANGE,
                    default => Skill::TYPE_TEAMWORK,
                };

                $existing = Skill::query()
                    ->where('user_id', $student->id)
                    ->whereRaw('LOWER(TRIM(skill_name)) = ?', [Str::lower($item['name'])])
                    ->first();

                if ($existing) {
                    continue;
                }

                $exchangeNeeded = null;
                $collaborationGoal = null;

                if ($skillType === Skill::TYPE_EXCHANGE) {
                    $target = collect($catalog)
                        ->reject(fn ($entry) => Str::lower($entry['name']) === Str::lower($item['name']))
                        ->random();

                    $exchangeNeeded = $target['name'];
                }

                if ($skillType === Skill::TYPE_TEAMWORK) {
                    $collaborationGoal = fake()->randomElement([
                        'Build a Laravel project',
                        'Prepare for exam',
                        'Create a portfolio app',
                        'Practice debate sessions',
                        'Organize a weekend coding sprint',
                        'Train together for tournament',
                    ]);
                }

                $skill = Skill::create([
                    'user_id' => $student->id,
                    'skill_name' => $item['name'],
                    'description' => fake()->sentence(14),
                    'category' => $item['category'],
                    'skill_type' => $skillType,
                    'skill_level' => fake()->randomElement(['Beginner', 'Intermediate', 'Advanced']),
                    'availability' => fake()->randomElement([
                        'Weekdays evening',
                        'Weekends',
                        'After class',
                        'Flexible',
                        'Online only',
                    ]),
                    'exchange_skill_needed' => $exchangeNeeded,
                    'collaboration_goal' => $collaborationGoal,
                    'auto_created_from_exchange' => false,
                    'exchange_parent_skill_id' => null,
                ]);

                if ($skill->normalized_type === Skill::TYPE_EXCHANGE && !empty($exchangeNeeded)) {
                    $this->syncExchangeWantedSkill($skill, $exchangeNeeded);
                }
            }
        }
    }

    private function syncExchangeWantedSkill(Skill $exchangeSkill, string $neededSkill): void
    {
        $existingWanted = Skill::query()
            ->where('user_id', $exchangeSkill->user_id)
            ->where('skill_type', Skill::TYPE_WANT_TO_LEARN)
            ->whereRaw('LOWER(TRIM(skill_name)) = ?', [Str::lower(trim($neededSkill))])
            ->first();

        $payload = [
            'user_id' => $exchangeSkill->user_id,
            'skill_name' => trim($neededSkill),
            'description' => 'Added automatically because user wants this skill in exchange for ' . $exchangeSkill->skill_name . '.',
            'category' => $exchangeSkill->category,
            'skill_type' => Skill::TYPE_WANT_TO_LEARN,
            'skill_level' => $exchangeSkill->skill_level,
            'availability' => $exchangeSkill->availability,
            'auto_created_from_exchange' => true,
            'exchange_parent_skill_id' => $exchangeSkill->id,
        ];

        if ($existingWanted) {
            $existingWanted->update([
                'category' => $payload['category'],
                'skill_level' => $payload['skill_level'],
                'availability' => $payload['availability'],
            ]);

            return;
        }

        Skill::create($payload);
    }
}
