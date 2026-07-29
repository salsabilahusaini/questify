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
            'micro_exercise_questions',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('material_block_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('type', 30)
                    ->default('multiple_choice');

                $table->text('question_text');

                $table->unsignedInteger('sequence');

                $table->unsignedSmallInteger('points')
                    ->default(10);

                $table->text('correct_feedback')
                    ->nullable();

                $table->text('incorrect_feedback')
                    ->nullable();

                $table->boolean('is_active')
                    ->default(true);

                $table->timestamps();

                $table->unique([
                    'material_block_id',
                    'sequence',
                ]);
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('micro_exercise_questions');
    }
};
