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
     * Define the relationship to the study group model.
     */
    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }

    /**
     * Define the relationship to the user model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
