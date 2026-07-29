<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroExerciseOption extends Model
{
    protected $fillable = [
        'micro_exercise_question_id',
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
            MicroExerciseQuestion::class,
            'micro_exercise_question_id'
        );
    }
}
