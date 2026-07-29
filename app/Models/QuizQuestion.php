<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';

    protected $fillable = [
        'mission_id',
        'type',
        'question_text',
        'sequence',
        'points',
        'explanation',
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
     * Mendapatkan misi pemilik soal Kuis Materi.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
    /**
 * Mendapatkan seluruh pilihan jawaban pada soal Kuis Materi.
 */
public function options(): HasMany
{
    return $this->hasMany(
        QuizOption::class,
        'quiz_question_id'
    )->orderBy('sequence');
}
}
