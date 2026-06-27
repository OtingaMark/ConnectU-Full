<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'user_id',
        'skill_name',
        'description',
        'category',
        'skill_type',
        'skill_level',
        'availability',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}