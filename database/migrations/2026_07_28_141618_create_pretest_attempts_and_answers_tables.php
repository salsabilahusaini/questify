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
        Schema::create('pretest_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('mission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status')
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

            $table->timestamps();

            $table->unique([
                'user_id',
                'mission_id',
            ]);
        });

        Schema::create('pretest_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pretest_attempt_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('pretest_question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('selected_option_id')
                ->constrained('pretest_options')
                ->cascadeOnDelete();

            $table->boolean('is_correct')
                ->default(false);

            $table->timestamps();

            $table->unique([
                'pretest_attempt_id',
                'pretest_question_id',
            ]);
        });
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('pretest_answers');
        Schema::dropIfExists('pretest_attempts');
    }
};
