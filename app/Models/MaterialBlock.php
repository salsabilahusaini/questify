<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialBlock extends Model
{
    public const TYPE_HEADING = 'heading';

    public const TYPE_PARAGRAPH = 'paragraph';

    public const TYPE_CODE = 'code';

    public const TYPE_IMAGE = 'image';

    public const TYPE_CHECKPOINT = 'checkpoint';

    protected $fillable = [
        'material_section_id',
        'type',
        'title',
        'content',
        'code_language',
        'media_path',
        'sequence',
        'minimum_read_seconds',
        'is_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'minimum_read_seconds' => 'integer',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mendapatkan bagian materi pemilik blok.
     */
    public function materialSection(): BelongsTo
    {
        return $this->belongsTo(MaterialSection::class);
    }
    /**
 * Mendapatkan seluruh progres siswa pada blok materi.
 */
public function progresses(): HasMany
{
    return $this->hasMany(MaterialBlockProgress::class);
}
/**
 * Mendapatkan seluruh Latihan Mikro pada blok materi.
 */
public function microExerciseQuestions(): HasMany
{
    return $this->hasMany(MicroExerciseQuestion::class)
        ->orderBy('sequence');
}
}
