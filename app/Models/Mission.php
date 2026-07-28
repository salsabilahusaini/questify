<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'sequence',
        'xp_reward',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'xp_reward' => 'integer',
            'is_published' => 'boolean',
        ];
    }
}
