<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupInvitation extends Model
{
    /**
     * Handle sender.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Handle receiver.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    
    protected $fillable = [
        'study_group_id',
        'sender_id',
        'receiver_id',
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
     * Handle update status.
     */
    public function updateStatus($status)
    {
        return $this->update([
            'status' => $status
        ]);
    }
}
