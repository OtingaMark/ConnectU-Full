<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\Report;
use App\Models\Skill;
use App\Models\StudyGroup;
use App\Models\SuspensionAppeal;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoReportSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@connectu.com')->first();
        $students = User::query()->where('role', 'student')->get();
        $activeStudents = $students->where('status', 'active')->values();
        $suspendedStudent = $students->firstWhere('status', 'suspended');

        if (!$admin || $activeStudents->count() < 3) {
            return;
        }

        $userTarget = $activeStudents->first();
        $userReporter = $activeStudents->where('id', '!=', $userTarget->id)->first();

        Report::create([
            'reporter_id' => $userReporter->id,
            'reported_user_id' => $userTarget->id,
            'reason' => 'Harassment',
            'description' => 'Repeatedly sending unwanted messages in direct chat (demo data).',
            'status' => 'pending',
        ]);

        $group = StudyGroup::query()->inRandomOrder()->first();

        if ($group) {
            $reporter = $activeStudents->where('id', '!=', $group->user_id)->first();

            Report::create([
                'reporter_id' => $reporter->id,
                'study_group_id' => $group->id,
                'reported_user_id' => $group->user_id,
                'reason' => 'Spam',
                'description' => 'Group posts were off-topic and repetitive (demo report).',
                'status' => 'reviewed',
                'admin_notes' => 'Under moderation review.',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now()->subDays(1),
            ]);
        }

        $skill = Skill::query()->where('user_id', '!=', $userReporter->id)->inRandomOrder()->first();

        if ($skill) {
            Report::create([
                'reporter_id' => $userReporter->id,
                'skill_id' => $skill->id,
                'reported_user_id' => $skill->user_id,
                'reason' => 'Inappropriate Content',
                'description' => 'Skill description looked misleading (demo report).',
                'status' => 'resolved',
                'admin_notes' => 'Resolved after warning and content cleanup.',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now()->subHours(12),
            ]);
        }

        $feedback = Feedback::query()->where('feedback_type', 'peer')->inRandomOrder()->first();

        if ($feedback) {
            $reporterId = $feedback->receiver_id;

            if ((int) $reporterId === (int) $feedback->giver_id) {
                $reporterId = $activeStudents->where('id', '!=', $feedback->giver_id)->first()?->id;
            }

            if ($reporterId) {
                Report::create([
                    'reporter_id' => $reporterId,
                    'feedback_id' => $feedback->id,
                    'reported_user_id' => $feedback->giver_id,
                    'reason' => 'Inappropriate Feedback',
                    'description' => 'Feedback tone appeared unconstructive (demo report).',
                    'status' => 'rejected',
                    'admin_notes' => 'Rejected: feedback is within policy.',
                    'reviewed_by' => $admin->id,
                    'reviewed_at' => now()->subHours(8),
                ]);
            }
        }

        if ($suspendedStudent) {
            SuspensionAppeal::updateOrCreate(
                [
                    'user_id' => $suspendedStudent->id,
                    'reason' => 'Request reinstatement',
                ],
                [
                    'message' => 'I understand the issue and will follow guidelines going forward.',
                    'status' => 'reviewed',
                    'admin_response' => 'Appeal reviewed. Further activity monitoring enabled.',
                    'reviewed_by' => $admin->id,
                    'reviewed_at' => now()->subHours(6),
                ]
            );
        }
    }
}
