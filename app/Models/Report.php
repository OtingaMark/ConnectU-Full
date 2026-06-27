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

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }

    public function groupMessage()
    {
        return $this->belongsTo(GroupMessage::class);
    }

    public function directMessage()
    {
        return $this->belongsTo(Message::class, 'direct_message_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
