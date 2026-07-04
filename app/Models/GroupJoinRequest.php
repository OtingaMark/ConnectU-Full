<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupJoinRequest extends Model
{
    protected $fillable = [
        'study_group_id',
        'user_id',
        'status',
    ];

    /**
     * Handle study group.
     */
    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }

    /**
     * Handle user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
