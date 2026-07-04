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
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Handle reporter.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Handle reported user.
     */
    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /**
     * Handle study group.
     */
    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }

    /**
     * Handle group message.
     */
    public function groupMessage()
    {
        return $this->belongsTo(GroupMessage::class);
    }

    /**
     * Handle direct message.
     */
    public function directMessage()
    {
        return $this->belongsTo(Message::class, 'direct_message_id');
    }

    /**
     * Handle feedback.
     */
    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    /**
     * Handle skill.
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Handle reviewer.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
