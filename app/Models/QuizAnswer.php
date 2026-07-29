<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    protected $fillable = [
        'quiz_attempt_id',
        'quiz_question_id',
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
     * Mendapatkan pengerjaan Kuis Materi pemilik jawaban.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(
            QuizAttempt::class,
            'quiz_attempt_id'
        );
    }

    /**
     * Mendapatkan soal yang dijawab.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(
            QuizQuestion::class,
            'quiz_question_id'
        );
    }

    /**
     * Mendapatkan pilihan jawaban yang dipilih siswa.
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(
            QuizOption::class,
            'selected_option_id'
        );
    }
}
