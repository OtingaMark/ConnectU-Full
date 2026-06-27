<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSuspensionAppeal extends Model
{
    protected $fillable = [
        'study_group_id',
        'requester_id',
        'reason',
        'message',
        'status',
        'admin_response',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
