<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasTeams;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'role', 'status', 'suspension_reason', 'suspended_at', 'password', 'current_team_id', 'theme_mode', 'accent_color'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasTeams, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Check whether the user account is currently suspended.
     */
    public function isSuspended(): bool
    {
        return strtolower(trim($this->status ?? 'active')) === 'suspended';
    }

    /**
     * Scope query results to active records.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

        /**
         * Execute the accent color operation for this method.
         */
        public function accentColor(): string
        {
            return match ($this->accent_color) {
                'green' => 'green',
                'purple' => 'purple',
                'pink' => 'pink',
                default => 'blue',
            };
        }

    /**
     * Execute the profile operation for this method.
     */
    public function profile()
{
    return $this->hasOne(Profile::class);
}

/**
 * Execute the study groups operation for this method.
 */
public function studyGroups()
{
    return $this->hasMany(StudyGroup::class);
}

/**
 * Display the skill management listing.
 */
public function skills()
{
    return $this->hasMany(Skill::class);
}

/**
 * Execute the resources operation for this method.
 */
public function resources()
{
    return $this->hasMany(Resource::class);
}

/**
 * Define the relationship to direct messages sent by this user.
 */
public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

/**
 * Define the relationship to direct messages received by this user.
 */
public function receivedMessages()
{
    return $this->hasMany(Message::class, 'receiver_id');
}

/**
 * Define the relationship to feedback records given by this user.
 */
public function feedbackGiven()
{
    return $this->hasMany(Feedback::class, 'giver_id');
}

/**
 * Define the relationship to feedback records received by this user.
 */
public function feedbackReceived()
{
    return $this->hasMany(Feedback::class, 'receiver_id');
}
/**
 * Define the relationship to connection requests sent by this user.
 */
public function sentConnections()
{
    return $this->hasMany(PeerConnection::class, 'requester_id');
}

/**
 * Define the relationship to connection requests received by this user.
 */
public function receivedConnections()
{
    return $this->hasMany(PeerConnection::class, 'receiver_id');
}

/**
 * Define the relationship to group invitations received by this user.
 */
public function receivedGroupInvitations()
{
    return $this->hasMany(GroupInvitation::class, 'receiver_id');
}

/**
 * Define the relationship to group invitations sent by this user.
 */
public function sentGroupInvitations()
{
    return $this->hasMany(GroupInvitation::class, 'sender_id');
}

/**
 * Define the relationship to group messages sent by this user.
 */
public function groupMessages()
{
    return $this->hasMany(GroupMessage::class);
}

/**
 * Define the relationship to reports submitted by this user.
 */
public function reportsSubmitted()
{
    return $this->hasMany(Report::class, 'reporter_id');
}

/**
 * Define the relationship to reports where this user is the target.
 */
public function reportsReceived()
{
    return $this->hasMany(Report::class, 'reported_user_id');
}

/**
 * Define the relationship to suspension appeals submitted by this user.
 */
public function suspensionAppeals()
{
    return $this->hasMany(SuspensionAppeal::class);
}

/**
 * Define the relationship to appeals reviewed by this user.
 */
public function reviewedAppeals()
{
    return $this->hasMany(SuspensionAppeal::class, 'reviewed_by');
}

/**
 * Define the relationship to group appeals submitted by this user.
 */
public function submittedGroupAppeals()
{
    return $this->hasMany(GroupSuspensionAppeal::class, 'requester_id');
}
}
