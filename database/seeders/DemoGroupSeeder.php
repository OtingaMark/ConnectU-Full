<?php

namespace Database\Seeders;

use App\Models\GroupInvitation;
use App\Models\GroupJoinRequest;
use App\Models\GroupMember;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'Java Programming', 'course' => 'Computer Science', 'description' => 'Hands-on Java practice and exam revision sessions.'],
            ['name' => 'Laravel Developers', 'course' => 'Software Engineering', 'description' => 'Build Laravel apps and review architecture patterns together.'],
            ['name' => 'Database Systems', 'course' => 'Business Information Technology', 'description' => 'Schema design, SQL drills, and optimization labs.'],
            ['name' => 'Automata Theory', 'course' => 'Informatics and Computer Science', 'description' => 'Theory revision, proofs, and model solving.'],
            ['name' => 'Cyber Security Club', 'course' => 'Cyber Security', 'description' => 'Security labs, CTF prep, and threat analysis discussions.'],
            ['name' => 'AI Research Circle', 'course' => 'Data Science', 'description' => 'Weekly AI paper reviews and mini experiments.'],
            ['name' => 'Data Science Team', 'course' => 'Data Science', 'description' => 'Collaborate on real datasets and analytics projects.'],
            ['name' => 'German Learners', 'course' => 'Communication', 'description' => 'Practice German speaking, grammar, and vocabulary.'],
            ['name' => 'Public Speaking Club', 'course' => 'Communication', 'description' => 'Speech practice and confident delivery workshops.'],
            ['name' => 'Chess Club', 'course' => 'Commerce', 'description' => 'Friendly chess games, tactics, and tournaments.'],
            ['name' => 'Football Team', 'course' => 'Sports', 'description' => 'Training sessions and weekend matches.'],
            ['name' => 'Chelsea Fans', 'course' => 'Community', 'description' => 'Match analysis and social hangouts for fans.'],
            ['name' => 'Basketball Team', 'course' => 'Sports', 'description' => 'Team drills and inter-campus game prep.'],
            ['name' => 'Photography Club', 'course' => 'Design', 'description' => 'Photo walks, editing practice, and portfolio reviews.'],
            ['name' => 'Music Production', 'course' => 'Design', 'description' => 'Beat making, mixing, and production collaboration.'],
            ['name' => 'UI/UX Designers', 'course' => 'Design', 'description' => 'Wireframing, prototyping, and usability feedback sessions.'],
            ['name' => 'Hackathon Team', 'course' => 'Software Engineering', 'description' => 'Hackathon prep with rapid MVP building and pitching.'],
        ];

        $students = User::query()->where('role', 'student')->get();
        $activeStudents = $students->where('status', 'active')->values();

        foreach ($groups as $index => $groupData) {
            $creator = $activeStudents->random();

            $group = StudyGroup::factory()->create([
                'user_id' => $creator->id,
                'group_name' => $groupData['name'],
                'course' => $groupData['course'],
                'description' => $groupData['description'],
                'meeting_schedule' => fake()->randomElement([
                    'Mon 6:00 PM',
                    'Tue 5:30 PM',
                    'Wed 7:00 PM',
                    'Thu 6:30 PM',
                    'Fri 4:30 PM',
                    'Sat 10:00 AM',
                ]),
                'visibility' => fake()->randomElement(['public', 'private']),
                'requires_approval' => fake()->boolean(40),
                'members_can_invite' => fake()->boolean(50),
                'max_members' => rand(12, 30),
                'group_picture' => 'images/groups/group-' . (($index % 10) + 1) . '.png',
                'status' => 'active',
            ]);

            GroupMember::firstOrCreate([
                'study_group_id' => $group->id,
                'user_id' => $creator->id,
            ], [
                'role' => 'admin',
                'joined_at' => now()->subDays(rand(20, 120)),
            ]);

            $targetMembers = rand(4, 15);
            $extraMembers = $students
                ->where('id', '!=', $creator->id)
                ->shuffle()
                ->take(max(0, $targetMembers - 1));

            foreach ($extraMembers as $member) {
                GroupMember::firstOrCreate([
                    'study_group_id' => $group->id,
                    'user_id' => $member->id,
                ], [
                    'role' => 'member',
                    'joined_at' => now()->subDays(rand(5, 100)),
                ]);
            }
        }

        $someGroup = StudyGroup::query()->inRandomOrder()->first();
        $sender = $activeStudents->random();
        $receiver = $activeStudents->where('id', '!=', $sender->id)->random();

        if ($someGroup && $sender && $receiver) {
            GroupInvitation::firstOrCreate([
                'study_group_id' => $someGroup->id,
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
            ], [
                'status' => 'pending',
            ]);
        }

        $approvalGroup = StudyGroup::query()->where('requires_approval', true)->inRandomOrder()->first();
        $approvalUser = $activeStudents->random();

        if ($approvalGroup && $approvalUser) {
            GroupJoinRequest::updateOrCreate([
                'study_group_id' => $approvalGroup->id,
                'user_id' => $approvalUser->id,
            ], [
                'status' => 'approved',
            ]);
        }
    }
}
