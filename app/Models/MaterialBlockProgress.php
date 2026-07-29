<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialBlockProgress extends Model
{
    protected $table = 'material_block_progresses';
    protected $fillable = [
        'user_id',
        'material_block_id',
        'first_opened_at',
        'last_activity_at',
        'total_read_seconds',
        'visit_count',
        'is_completed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'first_opened_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'total_read_seconds' => 'integer',
            'visit_count' => 'integer',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Mendapatkan siswa pemilik progres.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan blok materi yang dipelajari.
     */
    public function materialBlock(): BelongsTo
    {
        return $this->belongsTo(MaterialBlock::class);
    }
}
