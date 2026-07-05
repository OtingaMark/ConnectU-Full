<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeerConnection extends Model
{
    protected $fillable = [
        'requester_id',
        'receiver_id',
        'status',
    ];

    /**
     * Define the relationship to the requester user.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Define the relationship to the receiver user.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}