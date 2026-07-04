<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuspensionAppeal extends Model
{
    protected $fillable = [
        'user_id',
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
     * Handle user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Handle reviewer.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
