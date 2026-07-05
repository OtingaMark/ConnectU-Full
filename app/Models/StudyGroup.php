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
     * Check whether the user account is currently suspended.
     */
    public function isSuspended(): bool
    {
        return strtolower(trim($this->status ?? 'active')) === 'suspended';
    }

    /**
     * Define the relationship to the user model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Execute the members operation for this method.
     */
    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Execute the invitations operation for this method.
     */
    public function invitations()
    {
        return $this->hasMany(GroupInvitation::class);
    }

    /**
     * Execute the messages operation for this method.
     */
    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    /**
     * Execute the join requests operation for this method.
     */
    public function joinRequests()
    {
        return $this->hasMany(GroupJoinRequest::class);
    }

    /**
     * Display the moderation report listing.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Define the relationship to suspension appeals submitted by this user.
     */
    public function suspensionAppeals()
    {
        return $this->hasMany(GroupSuspensionAppeal::class);
    }
}