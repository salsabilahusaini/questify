<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionCompletion extends Model
{
    protected $fillable = [
        'user_id',
        'mission_id',
        'earned_xp',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'earned_xp' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Mendapatkan siswa yang menyelesaikan misi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan misi yang telah diselesaikan.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
