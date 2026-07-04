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
     * Handle requester.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Handle receiver.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}