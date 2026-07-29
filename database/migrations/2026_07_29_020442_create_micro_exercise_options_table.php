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
            'micro_exercise_options',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('micro_exercise_question_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->text('option_text');

                $table->unsignedInteger('sequence');

                $table->boolean('is_correct')
                    ->default(false);

                $table->timestamps();

                $table->unique(
                [
                    'micro_exercise_question_id',
                    'sequence',
                ],
                'micro_ex_opts_question_sequence_unique'
            );
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('micro_exercise_options');
    }
};
