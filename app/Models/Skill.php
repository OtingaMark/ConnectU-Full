<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'user_id',
        'skill_name',
        'description',
        'skill_level',
        'availability',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}