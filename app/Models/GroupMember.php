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
     * Define the relationship to the user model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship to the study group model.
     */
    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }
}