<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizOption extends Model
{
    protected $fillable = [
        'quiz_question_id',
        'option_text',
        'sequence',
        'is_correct',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'is_correct' => 'boolean',
        ];
    }

    /**
     * Mendapatkan soal pemilik pilihan jawaban.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(
            QuizQuestion::class,
            'quiz_question_id'
        );
    }
}
