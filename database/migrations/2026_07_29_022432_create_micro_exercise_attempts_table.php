<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration.
     */
    public function up(): void
    {
        Schema::create(
            'micro_exercise_attempts',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('mission_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('status', 20)
                    ->default('in_progress');

                $table->timestamp('started_at');

                $table->timestamp('completed_at')
                    ->nullable();

                $table->unsignedInteger('total_questions')
                    ->default(0);

                $table->unsignedInteger('correct_answers')
                    ->default(0);

                $table->decimal('score', 5, 2)
                    ->nullable();

                $table->unsignedInteger('earned_points')
                    ->default(0);

                $table->timestamps();

                $table->unique(
                    [
                        'user_id',
                        'mission_id',
                    ],
                    'micro_attempt_user_mission_unique'
                );
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('micro_exercise_attempts');
    }
};
