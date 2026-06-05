<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyGroup extends Model
{
    protected $fillable = [
        'user_id',
        'group_name',
        'course',
        'description',
        'max_members',
        'meeting_schedule',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }
}