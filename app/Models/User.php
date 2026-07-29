<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_TEACHER = 'teacher';

    public const ROLE_STUDENT = 'student';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
 * Memeriksa apakah pengguna memiliki role tertentu.
 */
public function hasRole(string $role): bool
{
    return $this->role === $role;
}

/**
 * Memeriksa apakah pengguna merupakan admin.
 */
public function isAdmin(): bool
{
    return $this->hasRole(self::ROLE_ADMIN);
}

/**
 * Memeriksa apakah pengguna merupakan guru.
 */
public function isTeacher(): bool
{
    return $this->hasRole(self::ROLE_TEACHER);
}

/**
 * Memeriksa apakah pengguna merupakan peserta didik.
 */
public function isStudent(): bool
{
    return $this->hasRole(self::ROLE_STUDENT);
}
/**
 * Mendapatkan seluruh pengerjaan Pretest pengguna.
 */
public function pretestAttempts(): HasMany
{
    return $this->hasMany(PretestAttempt::class);
}
/**
 * Mendapatkan seluruh progres pembacaan blok materi pengguna.
 */
public function materialBlockProgresses(): HasMany
{
    return $this->hasMany(MaterialBlockProgress::class);
}
/**
 * Mendapatkan seluruh pengerjaan Latihan Mikro pengguna.
 */
public function microExerciseAttempts(): HasMany
{
    return $this->hasMany(MicroExerciseAttempt::class);
}
/**
 * Mendapatkan seluruh pengerjaan Kuis Materi pengguna.
 */
public function quizAttempts(): HasMany
{
    return $this->hasMany(QuizAttempt::class);
}

/**
 * Mendapatkan seluruh pengerjaan Final Challenge pengguna.
 */
public function finalChallengeAttempts(): HasMany
{
    return $this->hasMany(
        FinalChallengeAttempt::class,
        'user_id'
    );
}

/**
 * Mendapatkan seluruh catatan penyelesaian misi pengguna.
 */
public function missionCompletions(): HasMany
{
    return $this->hasMany(
        MissionCompletion::class,
        'user_id'
    );
}
/**
 * Mendapatkan seluruh badge yang dimiliki pengguna.
 */
public function userBadges(): HasMany
{
    return $this->hasMany(
        UserBadge::class,
        'user_id'
    );
}
}
