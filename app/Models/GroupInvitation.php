<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupInvitation extends Model
{
    /**
     * Define the relationship to the sender user.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Define the relationship to the receiver user.
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
     * Define the relationship to the study group model.
     */
    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }

    /**
     * Update the model status to the provided value.
     */
    public function updateStatus($status)
    {
        return $this->update([
            'status' => $status
        ]);
    }
}
