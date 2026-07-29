<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    public const CRITERIA_MISSIONS_COMPLETED =
        'missions_completed';

    public const CRITERIA_TOTAL_XP =
        'total_xp';

    public const CRITERIA_PERFECT_CHALLENGE =
        'perfect_challenge';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'theme',
        'criteria_type',
        'criteria_value',
        'sequence',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'criteria_value' => 'integer',
            'sequence' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mendapatkan seluruh pemberian badge kepada siswa.
     */
    public function userBadges(): HasMany
    {
        return $this->hasMany(
            UserBadge::class,
            'badge_id'
        );
    }
}
