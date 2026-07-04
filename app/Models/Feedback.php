<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'giver_id',
        'receiver_id',
        'feedback_type',
        'rating',
        'comment',
    ];

    /**
     * Handle giver.
     */
    public function giver()
    {
        return $this->belongsTo(User::class, 'giver_id');
    }

    /**
     * Handle receiver.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Handle reports.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}