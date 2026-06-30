<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\GroupInvitation;
use App\Models\GroupJoinRequest;
use App\Models\Report;
use App\Models\Skill;
use App\Models\SuspensionAppeal;
use App\Models\User;
use App\Notifications\DemoPlatformNotification;
use Illuminate\Database\Seeder;

class DemoNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@connectu.com')->first();
        $students = User::query()->where('role', 'student')->get();
        $activeStudents = $students->where('status', 'active')->values();

        if ($activeStudents->isEmpty()) {
            return;
        }

        $invitation = GroupInvitation::query()->latest()->first();

        if ($invitation) {
            $receiver = User::query()->find($invitation->receiver_id);

            if ($receiver) {
                $receiver->notify(new DemoPlatformNotification(
                    'group_invitation',
                    'Group Invitation',
                    'You were invited to join a learning community.',
                    ['group_id' => $invitation->study_group_id]
                ));
            }
        }

        $joinApproval = GroupJoinRequest::query()->where('status', 'approved')->latest()->first();

        if ($joinApproval) {
            $joiner = User::query()->find($joinApproval->user_id);

            if ($joiner) {
                $joiner->notify(new DemoPlatformNotification(
                    'join_request_approved',
                    'Join Request Approved',
                    'Your request to join a group was approved.',
                    ['group_id' => $joinApproval->study_group_id]
                ));
            }
        }

        $skill = Skill::query()->where('skill_type', 'exchange')->inRandomOrder()->first();

        if ($skill) {
            $skillOwner = User::query()->find($skill->user_id);

            if ($skillOwner) {
                $skillOwner->notify(new DemoPlatformNotification(
                    'skill_request',
                    'Skill Exchange Request',
                    'A peer is interested in your exchange skill offer.',
                    ['skill_id' => $skill->id]
                ));
            }
        }

        Feedback::query()
            ->where('feedback_type', 'peer')
            ->latest()
            ->take(8)
            ->get()
            ->each(function (Feedback $feedback) {
                $receiver = User::query()->find($feedback->receiver_id);

                if ($receiver) {
                    $receiver->notify(new DemoPlatformNotification(
                        'feedback_received',
                        'New Peer Feedback',
                        'You received new anonymous peer feedback.',
                        ['feedback_id' => $feedback->id]
                    ));
                }
            });

        Report::query()->latest()->take(6)->get()->each(function (Report $report) {
            $reporter = User::query()->find($report->reporter_id);

            if ($reporter) {
                $reporter->notify(new DemoPlatformNotification(
                    'report_update',
                    'Report Update',
                    'Your moderation report status was updated to ' . ucfirst($report->status) . '.',
                    ['report_id' => $report->id, 'status' => $report->status]
                ));
            }
        });

        $appeal = SuspensionAppeal::query()->where('status', '!=', 'pending')->latest()->first();

        if ($appeal) {
            $user = User::query()->find($appeal->user_id);

            if ($user) {
                $user->notify(new DemoPlatformNotification(
                    'appeal_decision',
                    'Appeal Decision',
                    'Your suspension appeal status is now ' . ucfirst($appeal->status) . '.',
                    ['appeal_id' => $appeal->id, 'status' => $appeal->status]
                ));
            }
        }

        if ($admin) {
            $activeStudents->shuffle()->take(5)->each(function (User $student) use ($admin) {
                $admin->notify(new DemoPlatformNotification(
                    'system_activity',
                    'New Student Activity',
                    $student->name . ' recently contributed to a group discussion.',
                    ['student_id' => $student->id]
                ));
            });
        }
    }
}
