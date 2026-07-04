<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_name',
        'course',
        'description',
        'max_members',
        'meeting_schedule',
        'group_picture',
        'visibility',
        'requires_approval',
        'members_can_invite',
        'status',
        'suspension_reason',
        'suspended_at',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'members_can_invite' => 'boolean',
        'suspended_at' => 'datetime',
    ];

    /**
     * Handle is suspended.
     */
    public function isSuspended(): bool
    {
        return strtolower(trim($this->status ?? 'active')) === 'suspended';
    }

    /**
     * Handle user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Handle members.
     */
    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Handle invitations.
     */
    public function invitations()
    {
        return $this->hasMany(GroupInvitation::class);
    }

    /**
     * Handle messages.
     */
    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    /**
     * Handle join requests.
     */
    public function joinRequests()
    {
        return $this->hasMany(GroupJoinRequest::class);
    }

    /**
     * Handle reports.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Handle suspension appeals.
     */
    public function suspensionAppeals()
    {
        return $this->hasMany(GroupSuspensionAppeal::class);
    }
}