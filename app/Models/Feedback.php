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
     * Define the relationship to the feedback giver.
     */
    public function giver()
    {
        return $this->belongsTo(User::class, 'giver_id');
    }

    /**
     * Define the relationship to the receiver user.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Display the moderation report listing.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}