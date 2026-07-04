<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'course',
        'file_path',
        'resource_link',
    ];

    /**
     * Handle user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}