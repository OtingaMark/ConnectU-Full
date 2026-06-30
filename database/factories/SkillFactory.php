<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        $skillName = fake()->randomElement([
            'Java', 'Python', 'Laravel', 'PHP', 'MySQL', 'UI/UX',
            'Data Analysis', 'Cyber Security', 'Machine Learning',
            'Public Speaking', 'German', 'French', 'Graphic Design',
            'Photography', 'Football', 'Basketball', 'Chess',
            'Content Creation', 'Video Editing', 'Accounting', 'Mathematics',
        ]);

        return [
            'user_id' => User::factory(),
            'skill_name' => $skillName,
            'description' => fake()->sentence(12),
            'category' => fake()->randomElement([
                'Academic', 'Technical', 'Creative', 'Sports', 'Language', 'Business',
            ]),
            'skill_type' => fake()->randomElement([
                Skill::TYPE_CAN_TEACH,
                Skill::TYPE_WANT_TO_LEARN,
                Skill::TYPE_TEAMWORK,
            ]),
            'skill_level' => fake()->randomElement(['Beginner', 'Intermediate', 'Advanced']),
            'availability' => fake()->randomElement([
                'Weekdays evening',
                'Weekends',
                'After class',
                'Flexible schedule',
                'Online sessions',
            ]),
            'exchange_skill_needed' => null,
            'collaboration_goal' => null,
            'auto_created_from_exchange' => false,
            'exchange_parent_skill_id' => null,
        ];
    }
}
