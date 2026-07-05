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

    /**
     * Display the moderation report listing.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
