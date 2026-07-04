<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $fillable = [
        'user_id',
        'study_group_id',
        'role',
        'joined_at',
    ];

    /**
     * Handle user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Handle study group.
     */
    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }
}