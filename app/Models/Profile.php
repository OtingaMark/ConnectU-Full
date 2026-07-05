<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'course',
        'year_of_study',
        'bio',
        'interests',
        'skills',
        'availability',
        'profile_picture',
    ];

    /**
     * Define the relationship to the user model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}