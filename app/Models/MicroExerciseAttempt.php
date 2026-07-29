<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MicroExerciseAttempt extends Model
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
     * Mendapatkan siswa yang mengerjakan Latihan Mikro.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan misi tempat Latihan Mikro berada.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    /**
 * Mendapatkan seluruh jawaban pada pengerjaan Latihan Mikro.
 */
public function answers(): HasMany
{
    return $this->hasMany(
        MicroExerciseAnswer::class,
        'micro_exercise_attempt_id'
    );
}

    /**
     * Memeriksa apakah Latihan Mikro sudah diselesaikan.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
