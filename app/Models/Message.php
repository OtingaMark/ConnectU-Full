<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
    'sender_id',
    'receiver_id',
    'message',
    'file_path',
    'resource_link',
    'message_type',
    'is_read',
];

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

    /**
     * Display the moderation report listing.
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'direct_message_id');
    }
}