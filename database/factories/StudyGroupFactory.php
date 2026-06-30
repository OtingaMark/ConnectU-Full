<?php

namespace Database\Factories;

use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudyGroup>
 */
class StudyGroupFactory extends Factory
{
    protected $model = StudyGroup::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'group_name' => fake()->unique()->words(2, true),
            'course' => fake()->randomElement([
                'Informatics and Computer Science',
                'BBIT',
                'Computer Science',
                'Data Science',
                'Business Information Technology',
                'Software Engineering',
                'Cyber Security',
                'Commerce',
                'Communication',
                'Design',
            ]),
            'description' => fake()->sentence(18),
            'max_members' => fake()->numberBetween(8, 20),
            'meeting_schedule' => fake()->randomElement([
                'Mon & Wed 6:00 PM',
                'Tue 5:30 PM',
                'Fri 4:00 PM',
                'Saturday 10:00 AM',
                'Sunday 3:00 PM',
            ]),
            'group_picture' => null,
            'visibility' => fake()->randomElement(['public', 'private']),
            'requires_approval' => fake()->boolean(35),
            'members_can_invite' => fake()->boolean(45),
            'status' => 'active',
            'suspension_reason' => null,
            'suspended_at' => null,
        ];
    }
}
