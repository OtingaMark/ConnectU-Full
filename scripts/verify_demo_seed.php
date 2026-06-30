<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Feedback;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Report;
use App\Models\Skill;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

$checks = [
    'admin_exists' => User::where('email', 'admin@connectu.com')->where('role', 'admin')->exists() ? 'yes' : 'no',
    'mark_exists' => User::where('email', 'mark@gmail.com')->where('role', 'student')->exists() ? 'yes' : 'no',
    'students' => User::where('role', 'student')->count(),
    'suspended_students' => User::where('role', 'student')->where('status', 'suspended')->count(),
    'profiles' => Profile::count(),
    'skills' => Skill::count(),
    'exchange_skills' => Skill::where('skill_type', 'exchange')->count(),
    'auto_exchange_learn_skills' => Skill::where('auto_created_from_exchange', true)->count(),
    'groups' => StudyGroup::count(),
    'group_members' => GroupMember::count(),
    'direct_messages' => Message::count(),
    'group_messages' => GroupMessage::count(),
    'feedback_peer' => Feedback::where('feedback_type', 'peer')->count(),
    'feedback_app' => Feedback::where('feedback_type', 'app')->count(),
    'reports_total' => Report::count(),
    'reports_group' => Report::whereNotNull('study_group_id')->count(),
    'reports_skill' => Report::whereNotNull('skill_id')->count(),
    'reports_feedback' => Report::whereNotNull('feedback_id')->count(),
    'notifications' => class_exists(DatabaseNotification::class) ? DatabaseNotification::count() : 0,
];

foreach ($checks as $key => $value) {
    echo $key . '=' . $value . PHP_EOL;
}
