<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
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
        ];

        $years = ['Year 1', 'Year 2', 'Year 3', 'Year 4'];

        $admin = User::query()->where('email', 'admin@connectu.com')->first();

        if (!$admin) {
            $admin = User::factory()->create([
                'name' => 'ConnectU Admin',
                'email' => 'admin@connectu.com',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'status' => 'active',
                'suspension_reason' => null,
                'suspended_at' => null,
                'email_verified_at' => now(),
            ]);
        } else {
            $admin->update([
                'name' => 'ConnectU Admin',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'status' => 'active',
                'suspension_reason' => null,
                'suspended_at' => null,
            ]);
        }

        $mark = User::query()->where('email', 'mark@gmail.com')->first();

        if (!$mark) {
            $mark = User::factory()->create([
                'name' => 'Mark Otinga',
                'email' => 'mark@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        } else {
            $mark->update([
                'name' => 'Mark Otinga',
                'password' => Hash::make('12345678'),
                'role' => 'student',
                'status' => 'active',
                'suspension_reason' => null,
                'suspended_at' => null,
            ]);
        }

        $existingStudentCount = User::query()
            ->where('role', 'student')
            ->count();

        $toCreate = max(0, 50 - $existingStudentCount);

        if ($toCreate > 0) {
            User::factory($toCreate)->create([
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
            ]);
        }

        $students = User::query()
            ->where('role', 'student')
            ->orderBy('id')
            ->take(50)
            ->get();

        foreach ($students as $index => $student) {
            Profile::updateOrCreate(
                ['user_id' => $student->id],
                [
                    'course' => $courses[$index % count($courses)],
                    'year_of_study' => $years[array_rand($years)],
                    'bio' => fake()->sentence(18),
                    'interests' => implode(', ', fake()->randomElements([
                        'Coding',
                        'Hackathons',
                        'Design',
                        'Football',
                        'Photography',
                        'Public Speaking',
                        'Machine Learning',
                        'Data Analytics',
                        'Chess',
                        'Entrepreneurship',
                    ], rand(3, 5))),
                    'skills' => implode(', ', fake()->randomElements([
                        'Java', 'Python', 'Laravel', 'UI/UX', 'MySQL', 'Cyber Security',
                        'Content Creation', 'Accounting', 'Graphic Design', 'Video Editing',
                    ], rand(2, 4))),
                    'availability' => fake()->randomElement([
                        'Weekdays evening',
                        'Weekends',
                        'After classes',
                        'Flexible',
                        'Online sessions only',
                    ]),
                    'profile_picture' => 'images/avatars/avatar-' . (($index % 12) + 1) . '.png',
                ]
            );
        }

        $suspendedStudent = $students
            ->where('email', '!=', 'mark@gmail.com')
            ->first();

        if ($suspendedStudent) {
            $suspendedStudent->update([
                'status' => 'suspended',
                'suspension_reason' => 'Repeatedly posting irrelevant spam in group chats (demo suspension).',
                'suspended_at' => now()->subDays(2),
            ]);
        }
    }
}
