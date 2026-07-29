<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PretestAnswer extends Model
{
    protected $fillable = [
        'pretest_attempt_id',
        'pretest_question_id',
        'selected_option_id',
        'is_correct',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(PretestAttempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(
            PretestQuestion::class,
            'pretest_question_id'
        );
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(
            PretestOption::class,
            'selected_option_id'
        );
    }
}
