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
            'quiz_questions',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('mission_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('type', 30)
                    ->default('multiple_choice');

                $table->text('question_text');

                $table->unsignedInteger('sequence');

                $table->unsignedSmallInteger('points')
                    ->default(20);

                $table->text('explanation')
                    ->nullable();

                $table->boolean('is_active')
                    ->default(true);

                $table->timestamps();

                $table->unique(
                    [
                        'mission_id',
                        'sequence',
                    ],
                    'quiz_question_mission_sequence_unique'
                );
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
