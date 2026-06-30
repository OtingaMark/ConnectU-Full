<?php

namespace Database\Seeders;

use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoMessageSeeder extends Seeder
{
    public function run(): void
    {
        $directTemplates = [
            'Hey, are you free to revise Java today?',
            'Can you help me with Laravel routing?',
            'Let\'s meet after class.',
            'I saw your skill post, can we exchange UI design for PHP?',
            'Do you want to practice SQL joins this evening?',
            'Thanks for sharing the notes, they helped a lot.',
            'Can we pair for tomorrow\'s assignment?',
            'I can review your project slides tonight.',
            'Are you joining the study group call later?',
            'Let\'s do a quick recap before the quiz.',
        ];

        $groupTemplates = [
            'Who is available for revision tonight?',
            'I uploaded notes for the topic.',
            'Let\'s meet at the library.',
            'We need one more person for the project team.',
            'Please check the pinned resources before tomorrow.',
            'Can someone share the latest assignment brief?',
            'Reminder: meeting starts in 20 minutes.',
            'Great discussion today everyone.',
        ];

        $activeStudents = User::query()
            ->where('role', 'student')
            ->where('status', 'active')
            ->get();

        if ($activeStudents->count() >= 2) {
            for ($i = 0; $i < 280; $i++) {
                $sender = $activeStudents->random();
                $receiver = $activeStudents->where('id', '!=', $sender->id)->random();

                Message::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'message' => fake()->randomElement($directTemplates),
                    'message_type' => 'text',
                    'is_read' => fake()->boolean(65),
                    'created_at' => now()->subDays(rand(0, 30))->subMinutes(rand(0, 600)),
                    'updated_at' => now(),
                ]);
            }
        }

        $groups = StudyGroup::query()->get();

        foreach ($groups as $group) {
            $memberIds = GroupMember::query()
                ->where('study_group_id', $group->id)
                ->pluck('user_id');

            if ($memberIds->isEmpty()) {
                continue;
            }

            $messageCount = rand(8, 24);

            for ($j = 0; $j < $messageCount; $j++) {
                GroupMessage::create([
                    'study_group_id' => $group->id,
                    'user_id' => $memberIds->random(),
                    'message' => fake()->randomElement($groupTemplates),
                    'message_type' => 'text',
                    'created_at' => now()->subDays(rand(0, 20))->subMinutes(rand(0, 720)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
