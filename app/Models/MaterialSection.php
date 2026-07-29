<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialSection extends Model
{
    protected $fillable = [
        'mission_id',
        'title',
        'slug',
        'description',
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

    /**
     * Mendapatkan misi pemilik bagian materi.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
    /**
 * Mendapatkan seluruh blok pada bagian materi.
 */
public function blocks(): HasMany
{
    return $this->hasMany(MaterialBlock::class)
        ->orderBy('sequence');
}
}
