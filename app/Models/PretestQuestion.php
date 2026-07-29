<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PretestQuestion extends Model
{
    protected $fillable = [
        'mission_id',
        'question_text',
        'sequence',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PretestOption::class)
            ->orderBy('sequence');
    }
}
