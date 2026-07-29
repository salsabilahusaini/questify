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
        Schema::create('pretest_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('question_text');

            $table->unsignedInteger('sequence');

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'mission_id',
                'sequence',
            ]);
        });

        Schema::create('pretest_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pretest_question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('option_text');

            $table->unsignedInteger('sequence');

            $table->boolean('is_correct')
                ->default(false);

            $table->timestamps();

            $table->unique([
                'pretest_question_id',
                'sequence',
            ]);
        });
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('pretest_options');
        Schema::dropIfExists('pretest_questions');
    }
};
