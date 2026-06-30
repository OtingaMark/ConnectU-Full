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

    public function isSuspended(): bool
    {
        return strtolower(trim($this->status ?? 'active')) === 'suspended';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function invitations()
    {
        return $this->hasMany(GroupInvitation::class);
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    public function joinRequests()
    {
        return $this->hasMany(GroupJoinRequest::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function suspensionAppeals()
    {
        return $this->hasMany(GroupSuspensionAppeal::class);
    }
}