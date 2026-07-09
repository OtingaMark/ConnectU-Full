<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'study_group_id',
        'group_message_id',
        'direct_message_id',
        'feedback_id',
        'skill_id',
        'reason',
        'description',
        'evidence_path',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Define the relationship to the reporting user.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Define the relationship to the reported user.
     */
    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /**
     * Define the relationship to the associated study group.
     */
    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }

    /**
     * Define the relationship to the reported group message.
     */
    public function groupMessage()
    {
        return $this->belongsTo(GroupMessage::class);
    }

    /**
     * Define the relationship to the reported direct message.
     */
    public function directMessage()
    {
        return $this->belongsTo(Message::class, 'direct_message_id');
    }

    /**
     * Define the relationship to the feedback record referenced by this report.
     */
    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    /**
     * Define the relationship to the skill referenced by this report.
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Define the relationship to the admin reviewer.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
