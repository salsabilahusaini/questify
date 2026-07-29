<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'mission_id',
        'status',
        'started_at',
        'completed_at',
        'total_questions',
        'correct_answers',
        'score',
        'earned_points',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'total_questions' => 'integer',
            'correct_answers' => 'integer',
            'score' => 'decimal:2',
            'earned_points' => 'integer',
        ];
    }

    /**
     * Mendapatkan siswa yang mengerjakan Kuis Materi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan misi tempat Kuis Materi berada.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    /**
 * Mendapatkan seluruh jawaban pada pengerjaan Kuis Materi.
 */
public function answers(): HasMany
{
    return $this->hasMany(
        QuizAnswer::class,
        'quiz_attempt_id'
    );
}

    /**
     * Memeriksa apakah Kuis Materi telah diselesaikan.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
