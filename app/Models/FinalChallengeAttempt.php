<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalChallengeAttempt extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'mission_id',
        'final_challenge_id',
        'status',
        'answer_code',
        'answer_output',
        'answer_explanation',
        'started_at',
        'completed_at',
        'score',
        'earned_points',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'score' => 'decimal:2',
            'earned_points' => 'integer',
        ];
    }

    /**
     * Mendapatkan siswa yang mengerjakan Final Challenge.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan misi tempat Final Challenge berada.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * Mendapatkan Final Challenge yang dikerjakan.
     */
    public function finalChallenge(): BelongsTo
    {
        return $this->belongsTo(
            FinalChallenge::class,
            'final_challenge_id'
        );
    }

    /**
     * Memeriksa apakah Final Challenge sudah diselesaikan.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
