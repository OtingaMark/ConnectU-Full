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

    public function giver()
    {
        return $this->belongsTo(User::class, 'giver_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}