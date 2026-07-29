<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MicroExerciseQuestion extends Model
{
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';

    protected $fillable = [
        'material_block_id',
        'type',
        'question_text',
        'sequence',
        'points',
        'correct_feedback',
        'incorrect_feedback',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mendapatkan blok materi tempat latihan berada.
     */
    public function materialBlock(): BelongsTo
    {
        return $this->belongsTo(MaterialBlock::class);
    }

    /**
     * Mendapatkan seluruh pilihan jawaban soal.
     */
    public function options(): HasMany
    {
        return $this->hasMany(MicroExerciseOption::class)
            ->orderBy('sequence');
    }
}
