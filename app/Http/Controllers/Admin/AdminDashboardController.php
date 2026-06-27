<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudyGroup;
use App\Models\Message;
use App\Models\GroupMessage;
use App\Models\GroupSuspensionAppeal;
use App\Models\Report;
use App\Models\SuspensionAppeal;
use App\Models\Skill;
use App\Models\Feedback;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(
            auth()->check() && strtolower(trim(auth()->user()->role)) === 'admin',
            403
        );
    }

    public function index()
    {
        $this->ensureAdmin();

        return view('admin.dashboard', [
            'users' => User::count(),
            'groups' => StudyGroup::count(),
            'messages' => Message::count() + GroupMessage::count(),
            'skills' => Skill::count(),
            'feedback' => Feedback::count(),
        ]);
    }

    public function users()
    {
        $this->ensureAdmin();

        return view('admin.users.index', [
            'allUsers' => User::latest()->paginate(12),
        ]);
    }

    public function editUser(User $user)
    {
        $this->ensureAdmin();

        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function groups()
    {
        $this->ensureAdmin();

        return view('admin.groups.index', [
            'allGroups' => StudyGroup::with('user')->withCount('members')->latest()->paginate(12),
        ]);
    }

    public function editGroup(StudyGroup $studyGroup)
    {
        $this->ensureAdmin();

        return view('admin.groups.edit', [
            'studyGroup' => $studyGroup,
        ]);
    }

    public function updateGroup(Request $request, StudyGroup $studyGroup)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'group_name' => 'required|string|min:3|max:255',
            'course' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'required|integer|min:2|max:100',
            'meeting_schedule' => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
            'requires_approval' => 'nullable|boolean',
            'members_can_invite' => 'nullable|boolean',
        ]);

        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['members_can_invite'] = $request->has('members_can_invite');

        $studyGroup->update($validated);

        return redirect()->route('admin.groups.index')->with('success', 'Study group updated successfully.');
    }

    public function skills()
    {
        $this->ensureAdmin();

        return view('admin.skills.index', [
            'allSkills' => Skill::with('user')->latest()->paginate(12),
        ]);
    }

    public function feedback()
    {
        $this->ensureAdmin();

        return view('admin.feedback.index', [
            'allFeedback' => Feedback::with(['giver', 'receiver'])->latest()->paginate(12),
        ]);
    }

    public function reports()
    {
        $this->ensureAdmin();

        return view('admin.reports.index', [
            'totalUsers' => User::count(),
            'totalGroups' => StudyGroup::count(),
            'totalSkills' => Skill::count(),
            'totalFeedback' => Feedback::count(),
            'totalMessages' => Message::count() + GroupMessage::count(),
            'pendingCount' => Report::where('status', 'pending')->count(),
            'reviewedCount' => Report::where('status', 'reviewed')->count(),
            'resolvedCount' => Report::where('status', 'resolved')->count(),
            'rejectedCount' => Report::where('status', 'rejected')->count(),
            'actionableReports' => Report::with([
                'reporter',
                'reportedUser',
                'studyGroup',
                'groupMessage.user',
                'groupMessage.studyGroup',
                'directMessage.sender',
                'directMessage.receiver',
                'reviewer',
            ])
                ->whereIn('status', ['pending', 'reviewed'])
                ->latest()
                ->paginate(15, ['*'], 'actionable_reports_page'),
            'completedReports' => Report::with([
                'reporter',
                'reportedUser',
                'studyGroup',
                'groupMessage.user',
                'groupMessage.studyGroup',
                'directMessage.sender',
                'directMessage.receiver',
                'reviewer',
            ])
                ->whereIn('status', ['resolved', 'rejected'])
                ->latest()
                ->paginate(10, ['*'], 'completed_reports_page'),
            'appeals' => SuspensionAppeal::with(['user', 'reviewer'])
                ->latest()
                ->paginate(10, ['*'], 'user_appeals_page'),
            'groupAppeals' => GroupSuspensionAppeal::with(['studyGroup', 'requester', 'reviewer'])
                ->latest()
                ->paginate(10, ['*'], 'group_appeals_page'),
        ]);
    }

    public function generateReport()
    {
        $this->ensureAdmin();

        $contents = implode("\n", [
            'ConnectU Platform Report',
            'Generated At: ' . now()->toDateTimeString(),
            '---',
            'Total Users: ' . User::count(),
            'Total Learning Groups: ' . StudyGroup::count(),
            'Total Skills: ' . Skill::count(),
            'Total Feedback: ' . Feedback::count(),
            'Total Messages (count only): ' . (Message::count() + GroupMessage::count()),
            'Privacy Note: Admin report excludes private direct message content.',
        ]) . "\n";

        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, 'connectu-report-' . now()->format('Ymd-His') . '.txt', [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function analytics()
    {
        $this->ensureAdmin();

        return view('admin.analytics.index', [
            'users' => User::count(),
            'groups' => StudyGroup::count(),
            'skills' => Skill::count(),
            'feedback' => Feedback::count(),
            'messages' => Message::count() + GroupMessage::count(),
        ]);
    }

    public function changeRole(User $user)
    {
        $this->ensureAdmin();

        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->role =
            $user->role == 'admin'
            ? 'student'
            : 'admin';

        $user->save();

        return back()->with('success','Role updated.');
    }

    public function updateUser(Request $request, User $user)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:student,admin',
            'status' => 'required|in:active,suspended',
            'suspension_reason' => 'nullable|string|max:1000',
        ]);

        if (auth()->id() === $user->id && $validated['role'] !== 'admin') {
            return back()->with('error', 'You cannot demote your own admin account.');
        }

        if ($validated['status'] === 'suspended' && empty(trim($validated['suspension_reason'] ?? ''))) {
            return back()->with('error', 'Suspension reason is required when status is suspended.');
        }

        if ($validated['status'] === 'active') {
            $validated['suspension_reason'] = null;
            $validated['suspended_at'] = null;
        } else {
            $validated['suspended_at'] = $user->suspended_at ?? now();
        }

        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
    }

    public function suspendUser(User $user)
    {
        $this->ensureAdmin();

        $request = request();

        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        if (strtolower(trim($user->role ?? '')) === 'admin') {
            return back()->with('error', 'Suspend by first changing role from admin.');
        }

        if (($request->input('action') ?? '') === 'unsuspend') {
            $user->status = 'active';
            $user->suspension_reason = null;
            $user->suspended_at = null;
            $user->save();

            return back()->with('success', 'User unsuspended successfully.');
        }

        $validated = $request->validate([
            'suspension_reason' => 'required|string|min:5|max:1000',
        ]);

        $user->status = 'suspended';
        $user->suspension_reason = $validated['suspension_reason'];
        $user->suspended_at = now();
        $user->save();

        return back()->with('success', 'User suspended successfully.');
    }

    public function reviewReport(Request $request, Report $report)
    {
        $this->ensureAdmin();

        if ($this->isReportCompleted($report)) {
            return back()->with('error', 'This report is already completed.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $report->update([
            'status' => 'reviewed',
            'admin_notes' => $validated['admin_notes'] ?? $report->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Report marked as reviewed.');
    }

    public function resolveReport(Request $request, Report $report)
    {
        $this->ensureAdmin();

        if ($this->isReportCompleted($report)) {
            return back()->with('error', 'This report is already completed.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $report->update([
            'status' => 'resolved',
            'admin_notes' => $validated['admin_notes'] ?? $report->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Report resolved.');
    }

    public function rejectReport(Request $request, Report $report)
    {
        $this->ensureAdmin();

        if ($this->isReportCompleted($report)) {
            return back()->with('error', 'This report is already completed.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $report->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'] ?? $report->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Report rejected.');
    }

    public function suspendReportedUser(Request $request, Report $report)
    {
        $this->ensureAdmin();

        if ($this->isReportCompleted($report)) {
            return back()->with('error', 'This report is already completed.');
        }

        $validated = $request->validate([
            'suspension_reason' => 'required|string|min:5|max:1000',
        ]);

        $user = $report->reportedUser;

        if (!$user) {
            return back()->with('error', 'This report is not targeting a user account.');
        }

        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        if (strtolower(trim($user->role ?? '')) === 'admin') {
            return back()->with('error', 'Cannot suspend admin account from report action.');
        }

        $user->update([
            'status' => 'suspended',
            'suspension_reason' => $validated['suspension_reason'],
            'suspended_at' => now(),
        ]);

        $report->update([
            'status' => 'reviewed',
            'admin_notes' => trim(($report->admin_notes ? $report->admin_notes . "\n" : '') . 'User suspended via report action.'),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Reported user suspended successfully.');
    }

    public function suspendReportedGroup(Request $request, Report $report)
    {
        $this->ensureAdmin();

        if ($this->isReportCompleted($report)) {
            return back()->with('error', 'This report is already completed.');
        }

        $validated = $request->validate([
            'suspension_reason' => 'required|string|min:5|max:1000',
        ]);

        $group = $report->studyGroup;

        if (!$group) {
            return back()->with('error', 'This report is not targeting a study group.');
        }

        $group->update([
            'status' => 'suspended',
            'suspension_reason' => $validated['suspension_reason'],
            'suspended_at' => now(),
        ]);

        $report->update([
            'status' => 'reviewed',
            'admin_notes' => trim(($report->admin_notes ? $report->admin_notes . "\n" : '') . 'Group suspended via report action.'),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Reported group suspended successfully.');
    }

    public function deleteReportedContent(Report $report)
    {
        $this->ensureAdmin();

        if ($this->isReportCompleted($report)) {
            return back()->with('error', 'This report is already completed.');
        }

        $deleted = false;

        if ($report->groupMessage) {
            $report->groupMessage->delete();
            $deleted = true;
        } elseif ($report->directMessage) {
            $report->directMessage->delete();
            $deleted = true;
        } elseif ($report->studyGroup) {
            $report->studyGroup->delete();
            $deleted = true;
        }

        if (!$deleted) {
            return back()->with('error', 'No deletable content is attached to this report.');
        }

        $report->update([
            'status' => 'resolved',
            'admin_notes' => trim(($report->admin_notes ? $report->admin_notes . "\n" : '') . 'Reported content deleted by admin.'),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Reported content deleted and report resolved.');
    }

    public function approveAppeal(Request $request, SuspensionAppeal $appeal)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'admin_response' => 'nullable|string|max:2000',
        ]);

        $appeal->update([
            'status' => 'approved',
            'admin_response' => $validated['admin_response'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $appeal->user->update([
            'status' => 'active',
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);

        return back()->with('success', 'Appeal approved and user reactivated.');
    }

    public function rejectAppeal(Request $request, SuspensionAppeal $appeal)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'admin_response' => 'nullable|string|max:2000',
        ]);

        $appeal->update([
            'status' => 'rejected',
            'admin_response' => $validated['admin_response'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Appeal rejected.');
    }

    public function approveGroupAppeal(Request $request, GroupSuspensionAppeal $appeal)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'admin_response' => 'nullable|string|max:2000',
        ]);

        $appeal->update([
            'status' => 'approved',
            'admin_response' => $validated['admin_response'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $appeal->studyGroup->update([
            'status' => 'active',
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);

        return back()->with('success', 'Group appeal approved and group reactivated.');
    }

    public function rejectGroupAppeal(Request $request, GroupSuspensionAppeal $appeal)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'admin_response' => 'nullable|string|max:2000',
        ]);

        $appeal->update([
            'status' => 'rejected',
            'admin_response' => $validated['admin_response'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Group appeal rejected.');
    }

    private function resolveReportedUser(Report $report): ?User
    {
        if ($report->reportedUser) {
            return $report->reportedUser;
        }

        if ($report->directMessage?->sender) {
            return $report->directMessage->sender;
        }

        if ($report->groupMessage?->user) {
            return $report->groupMessage->user;
        }

        if ($report->studyGroup?->user) {
            return $report->studyGroup->user;
        }

        return null;
    }

    private function isReportCompleted(Report $report): bool
    {
        return in_array($report->status, ['resolved', 'rejected'], true);
    }

    public function deleteUser(User $user)
    {
        $this->ensureAdmin();

        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if (strtolower(trim($user->role ?? '')) === 'admin') {
            $otherAdmins = User::whereRaw('LOWER(TRIM(role)) = ?', ['admin'])
                ->where('id', '!=', $user->id)
                ->count();

            if ($otherAdmins === 0) {
                return back()->with('error', 'Cannot delete the last admin account.');
            }
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function deleteGroup(StudyGroup $studyGroup)
    {
        $this->ensureAdmin();

        $studyGroup->delete();

        return back()->with('success', 'Study group deleted successfully.');
    }
}
