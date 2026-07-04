<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    protected $fillable = [
        'study_group_id',
        'user_id',
        'message',
        'file_path',
        'resource_link',
        'message_type',
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

    /**
     * Handle reports.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
