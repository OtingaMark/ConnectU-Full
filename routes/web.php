<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudyGroupController;
use App\Http\Controllers\PeerMatchingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeerConnectionController;
use App\Http\Controllers\GroupInvitationController;
use App\Http\Controllers\UserSearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SuspensionAppealController;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/appeal-suspension', [SuspensionAppealController::class, 'create'])
    ->name('appeal-suspension.create');

Route::post('/appeal-suspension', [SuspensionAppealController::class, 'store'])
    ->name('appeal-suspension.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/admin/users', [AdminDashboardController::class, 'users'])
        ->name('admin.users.index');

    Route::get('/admin/users/{user}/edit', [AdminDashboardController::class, 'editUser'])
        ->name('admin.users.edit');

    Route::get('/admin/study-groups', [AdminDashboardController::class, 'groups'])
        ->name('admin.groups.index');

    Route::get('/admin/study-groups/{studyGroup}/edit', [AdminDashboardController::class, 'editGroup'])
        ->name('admin.study-groups.edit');

    Route::patch('/admin/study-groups/{studyGroup}', [AdminDashboardController::class, 'updateGroup'])
        ->name('admin.study-groups.update');

    Route::patch('/admin/study-groups/{studyGroup}/suspend', [AdminDashboardController::class, 'suspendGroup'])
        ->name('admin.groups.suspend');

    Route::get('/admin/skills', [AdminDashboardController::class, 'skills'])
        ->name('admin.skills.index');

    Route::get('/admin/feedback', [AdminDashboardController::class, 'feedback'])
        ->name('admin.feedback.index');

    Route::get('/admin/reports', [AdminDashboardController::class, 'reports'])
        ->name('admin.reports.index');

    Route::get('/admin/reports/generate', [AdminDashboardController::class, 'generateReport'])
        ->name('admin.reports.generate');

    Route::patch('/admin/reports/{report}/review', [AdminDashboardController::class, 'reviewReport'])
        ->name('admin.reports.review');

    Route::patch('/admin/reports/{report}/resolve', [AdminDashboardController::class, 'resolveReport'])
        ->name('admin.reports.resolve');

    Route::patch('/admin/reports/{report}/reject', [AdminDashboardController::class, 'rejectReport'])
        ->name('admin.reports.reject');

    Route::patch('/admin/reports/{report}/suspend-user', [AdminDashboardController::class, 'suspendReportedUser'])
        ->name('admin.reports.suspend-user');

    Route::patch('/admin/reports/{report}/suspend-group', [AdminDashboardController::class, 'suspendReportedGroup'])
        ->name('admin.reports.suspend-group');

    Route::delete('/admin/reports/{report}/delete-content', [AdminDashboardController::class, 'deleteReportedContent'])
        ->name('admin.reports.delete-content');

    Route::patch('/admin/appeals/{appeal}/approve', [AdminDashboardController::class, 'approveAppeal'])
        ->name('admin.appeals.approve');

    Route::patch('/admin/appeals/{appeal}/reject', [AdminDashboardController::class, 'rejectAppeal'])
        ->name('admin.appeals.reject');

    Route::patch('/admin/group-appeals/{appeal}/approve', [AdminDashboardController::class, 'approveGroupAppeal'])
        ->name('admin.group-appeals.approve');

    Route::patch('/admin/group-appeals/{appeal}/reject', [AdminDashboardController::class, 'rejectGroupAppeal'])
        ->name('admin.group-appeals.reject');

    Route::get('/admin/analytics', [AdminDashboardController::class, 'analytics'])
        ->name('admin.analytics.index');

    Route::post('/admin/users/{user}/role', [AdminDashboardController::class, 'changeRole'])
        ->name('admin.change-role');

    Route::patch('/admin/users/{user}', [AdminDashboardController::class, 'updateUser'])
        ->name('admin.users.update');

    Route::patch('/admin/users/{user}/suspend', [AdminDashboardController::class, 'suspendUser'])
        ->name('admin.users.suspend');

    Route::delete('/admin/users/{user}', [AdminDashboardController::class, 'deleteUser'])
        ->name('admin.users.delete');

    Route::delete('/admin/study-groups/{studyGroup}', [AdminDashboardController::class, 'deleteGroup'])
        ->name('admin.groups.delete');

    Route::delete('/admin/skills/{skill}', [AdminDashboardController::class, 'deleteSkill'])
        ->name('admin.skills.delete');
});

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/users/{user}', [ProfileController::class, 'show'])->name('users.show');
    Route::get('/appearance-settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/appearance-settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/find-people', [UserSearchController::class, 'index'])
        ->name('users.search');
    Route::post('/reports', [ReportController::class, 'store'])
        ->name('reports.store');
    Route::get('/study-groups', [StudyGroupController::class, 'index'])->name('study-groups.index');
    Route::get('/study-groups/create', [StudyGroupController::class, 'create'])->name('study-groups.create');
    Route::post('/study-groups', [StudyGroupController::class, 'store'])->name('study-groups.store');
    Route::get('/study-groups/{studyGroup}', [StudyGroupController::class, 'show'])->name('study-groups.show');
    Route::get('/study-groups/{studyGroup}/edit', [StudyGroupController::class, 'edit'])
        ->name('study-groups.edit');
    Route::patch('/study-groups/{studyGroup}', [StudyGroupController::class, 'update'])
        ->name('study-groups.update');
    Route::delete('/study-groups/{studyGroup}', [StudyGroupController::class, 'destroy'])
        ->name('study-groups.destroy');
    Route::post('/study-groups/{studyGroup}/invite', [StudyGroupController::class, 'invite'])
        ->name('study-groups.invite');
    Route::post('/study-groups/{studyGroup}/messages', [StudyGroupController::class, 'sendMessage'])
        ->name('study-groups.messages.store');
    Route::post('/study-groups/{studyGroup}/appeal-suspension', [StudyGroupController::class, 'appealSuspension'])
        ->name('study-groups.appeal-suspension');
    Route::post('/study-groups/{studyGroup}/join', [StudyGroupController::class, 'join'])->name('study-groups.join');
    Route::post('/study-groups/{studyGroup}/request-join', [StudyGroupController::class, 'requestToJoin'])
        ->name('study-groups.request-join');
    Route::delete('/study-groups/{studyGroup}/leave', [StudyGroupController::class, 'leave'])
        ->name('study-groups.leave');
    Route::patch('/study-groups/{studyGroup}/members/{member}/promote', [StudyGroupController::class, 'promoteMember'])
        ->name('study-groups.members.promote');
    Route::patch('/study-groups/{studyGroup}/members/{member}/demote', [StudyGroupController::class, 'demoteMember'])
        ->name('study-groups.members.demote');
    Route::delete('/study-groups/{studyGroup}/members/{member}/remove', [StudyGroupController::class, 'removeMember'])
        ->name('study-groups.members.remove');
    Route::get('/group-messages/{message}/attachment', [StudyGroupController::class, 'groupAttachment'])
        ->name('group-messages.attachment');
    Route::post('/group-invitations/{invitation}/accept', [GroupInvitationController::class, 'accept'])
        ->name('group-invitations.accept');
    Route::post('/group-invitations/{invitation}/decline', [GroupInvitationController::class, 'decline'])
        ->name('group-invitations.decline');
    Route::post('/group-join-requests/{joinRequest}/approve', [StudyGroupController::class, 'approveJoinRequest'])
        ->name('group-join-requests.approve');
    Route::post('/group-join-requests/{joinRequest}/decline', [StudyGroupController::class, 'declineJoinRequest'])
        ->name('group-join-requests.decline');
    Route::get('/peer-matching', [PeerMatchingController::class, 'index'])->name('peer-matching.index');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::get('/skills', [SkillController::class, 'index'])->name('skills.index');
    Route::post('/skills', [SkillController::class, 'store'])->name('skills.store');
    Route::get('/skills/{skill}/edit', [SkillController::class, 'edit'])->name('skills.edit');
    Route::patch('/skills/{skill}', [SkillController::class, 'update'])->name('skills.update');
    Route::patch('/skills/{skill}/toggle-status', [SkillController::class, 'toggleStatus'])->name('skills.toggle-status');
    Route::delete('/skills/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');
    Route::get('/skills/{skill}/matches', [SkillController::class, 'matches'])->name('skills.matches');
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/messages/{message}/attachment', [App\Http\Controllers\MessageController::class, 'attachment'])
    ->name('messages.attachment')
    ->middleware('auth');
    Route::post('/connections/send/{user}',
    [PeerConnectionController::class, 'send'])
    ->name('connections.send');

    Route::post('/connections/{connection}/accept',
    [PeerConnectionController::class, 'accept'])
    ->name('connections.accept');

    Route::post('/connections/{connection}/decline',
    [PeerConnectionController::class, 'decline'])
    ->name('connections.decline');
    Route::get(
        '/messages/groups/{studyGroup}',
        [MessageController::class, 'groupChat']
    )->name('messages.group');
});

require __DIR__.'/settings.php';
