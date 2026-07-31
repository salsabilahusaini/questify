<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function pretestQuestions(): HasMany
{
    return $this->hasMany(PretestQuestion::class)
        ->orderBy('sequence');
}
/**
 * Mendapatkan seluruh pengerjaan Pretest pada misi.
 */
public function pretestAttempts(): HasMany
{
    return $this->hasMany(PretestAttempt::class);
}
/**
 * Mendapatkan seluruh soal Posttest pada misi.
 */
public function posttestQuestions(): HasMany
{
    return $this->hasMany(PosttestQuestion::class)
        ->orderBy('sequence');
}

/**
 * Mendapatkan seluruh pengerjaan Posttest pada misi.
 */
public function posttestAttempts(): HasMany
{
    return $this->hasMany(PosttestAttempt::class);
}
/**
 * Mendapatkan seluruh bagian materi pada misi.
 */
public function materialSections(): HasMany
{
    return $this->hasMany(MaterialSection::class)
        ->orderBy('sequence');
}
/**
 * Mendapatkan seluruh pengerjaan Latihan Mikro pada misi.
 */
public function microExerciseAttempts(): HasMany
{
    return $this->hasMany(MicroExerciseAttempt::class);
}
/**
 * Mendapatkan seluruh soal Kuis Materi pada misi.
 */
public function quizQuestions(): HasMany
{
    return $this->hasMany(QuizQuestion::class)
        ->orderBy('sequence');
}

/**
 * Mendapatkan seluruh pengerjaan Kuis Materi pada misi.
 */
public function quizAttempts(): HasMany
{
    return $this->hasMany(QuizAttempt::class);
}

/**
 * Mendapatkan Final Challenge pada misi.
 */
public function finalChallenge(): HasOne
{
    return $this->hasOne(FinalChallenge::class);
}

/**
 * Mendapatkan seluruh pengerjaan Final Challenge pada misi.
 */
public function finalChallengeAttempts(): HasMany
{
    return $this->hasMany(
        FinalChallengeAttempt::class,
        'mission_id'
    );
}

/**
 * Mendapatkan seluruh catatan penyelesaian misi.
 */
public function completions(): HasMany
{
    return $this->hasMany(
        MissionCompletion::class,
        'mission_id'
    );
}
}
