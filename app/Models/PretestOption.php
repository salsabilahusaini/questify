<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PretestOption extends Model
{
    protected $fillable = [
        'pretest_question_id',
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

    public function question(): BelongsTo
    {
        return $this->belongsTo(
            PretestQuestion::class,
            'pretest_question_id'
        );
    }
}
