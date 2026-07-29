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
            'micro_exercise_answers',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('micro_exercise_attempt_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('micro_exercise_question_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('selected_option_id')
                    ->constrained('micro_exercise_options')
                    ->cascadeOnDelete();

                $table->boolean('is_correct')
                    ->default(false);

                $table->unsignedSmallInteger('awarded_points')
                    ->default(0);

                $table->timestamp('answered_at');

                $table->timestamps();

                $table->unique(
                    [
                        'micro_exercise_attempt_id',
                        'micro_exercise_question_id',
                    ],
                    'micro_answer_attempt_question_unique'
                );
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('micro_exercise_answers');
    }
};
