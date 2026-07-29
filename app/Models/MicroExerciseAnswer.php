<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroExerciseAnswer extends Model
{
    protected $fillable = [
        'micro_exercise_attempt_id',
        'micro_exercise_question_id',
        'selected_option_id',
        'is_correct',
        'awarded_points',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'awarded_points' => 'integer',
            'answered_at' => 'datetime',
        ];
    }

    /**
     * Mendapatkan pengerjaan Latihan Mikro pemilik jawaban.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(
            MicroExerciseAttempt::class,
            'micro_exercise_attempt_id'
        );
    }

    /**
     * Mendapatkan soal yang dijawab.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(
            MicroExerciseQuestion::class,
            'micro_exercise_question_id'
        );
    }

    /**
     * Mendapatkan pilihan jawaban yang dipilih siswa.
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(
            MicroExerciseOption::class,
            'selected_option_id'
        );
    }
}
