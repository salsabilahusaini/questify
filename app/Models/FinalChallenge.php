<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinalChallenge extends Model
{
    protected $fillable = [
        'mission_id',
        'title',
        'scenario',
        'instructions',
        'starter_code',
        'expected_output',
        'max_points',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mendapatkan misi pemilik Final Challenge.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
    /**
 * Mendapatkan seluruh pengerjaan Final Challenge.
 */
public function attempts(): HasMany
{
    return $this->hasMany(
        FinalChallengeAttempt::class,
        'final_challenge_id'
    );
}
}
