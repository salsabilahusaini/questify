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
            'quiz_answers',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('quiz_attempt_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('quiz_question_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('selected_option_id')
                    ->constrained('quiz_options')
                    ->cascadeOnDelete();

                $table->boolean('is_correct')
                    ->default(false);

                $table->unsignedInteger('awarded_points')
                    ->default(0);

                $table->timestamp('answered_at');

                $table->timestamps();

                $table->unique(
                    [
                        'quiz_attempt_id',
                        'quiz_question_id',
                    ],
                    'quiz_answer_attempt_question_unique'
                );
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
