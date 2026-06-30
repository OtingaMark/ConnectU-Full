<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoFeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $comments = [
            'Very reliable teammate.',
            'Explains concepts clearly.',
            'Helped me understand the topic.',
            'Good communication.',
            'Active in group discussions.',
            'Always prepared for revision sessions.',
            'Shares useful resources on time.',
            'Great collaboration during project work.',
            'Patient and supportive when helping others.',
        ];

        $students = User::query()
            ->where('role', 'student')
            ->where('status', 'active')
            ->get();

        $pairSeen = [];

        for ($i = 0; $i < 170; $i++) {
            $giver = $students->random();
            $receiver = $students->where('id', '!=', $giver->id)->random();
            $pairKey = $giver->id . '-' . $receiver->id;

            if (isset($pairSeen[$pairKey])) {
                continue;
            }

            $pairSeen[$pairKey] = true;

            Feedback::create([
                'giver_id' => $giver->id,
                'receiver_id' => $receiver->id,
                'feedback_type' => 'peer',
                'rating' => fake()->randomElement([3, 3, 4, 4, 4, 5, 5]),
                'comment' => fake()->randomElement($comments),
                'created_at' => now()->subDays(rand(0, 40)),
                'updated_at' => now(),
            ]);
        }

        $admin = User::query()->where('email', 'admin@connectu.com')->first();

        if ($admin) {
            $students->shuffle()->take(12)->each(function ($student) use ($admin) {
                Feedback::create([
                    'giver_id' => $student->id,
                    'receiver_id' => $admin->id,
                    'feedback_type' => 'app',
                    'rating' => fake()->randomElement([3, 4, 4, 5]),
                    'comment' => fake()->randomElement([
                        'The app is smooth and easy to use.',
                        'Please add more filters on groups.',
                        'Would love better mobile notification handling.',
                        'Great platform for collaboration so far.',
                        'A dark mode schedule option would be nice.',
                    ]),
                    'created_at' => now()->subDays(rand(0, 25)),
                    'updated_at' => now(),
                ]);
            });
        }
    }
}
