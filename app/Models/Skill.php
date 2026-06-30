<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skill extends Model
{
    use HasFactory;

    public const TYPE_CAN_TEACH = 'can_teach';
    public const TYPE_WANT_TO_LEARN = 'want_to_learn';
    public const TYPE_EXCHANGE = 'exchange';
    public const TYPE_TEAMWORK = 'teamwork';

    protected $fillable = [
        'user_id',
        'skill_name',
        'description',
        'category',
        'skill_type',
        'skill_level',
        'availability',
        'exchange_skill_needed',
        'collaboration_goal',
        'auto_created_from_exchange',
        'exchange_parent_skill_id',
    ];

    public static function normalizedType(?string $type): string
    {
        return match (strtolower(trim((string) $type))) {
            'teach', self::TYPE_CAN_TEACH => self::TYPE_CAN_TEACH,
            'learn', self::TYPE_WANT_TO_LEARN => self::TYPE_WANT_TO_LEARN,
            self::TYPE_EXCHANGE => self::TYPE_EXCHANGE,
            self::TYPE_TEAMWORK => self::TYPE_TEAMWORK,
            default => self::TYPE_CAN_TEACH,
        };
    }

    public function getNormalizedTypeAttribute(): string
    {
        return self::normalizedType($this->skill_type);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}