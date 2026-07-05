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

    /**
     * Define the relationship to the study group model.
     */
    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }

    /**
     * Define the relationship to the requester user.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Define the relationship to the reviewer user.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
