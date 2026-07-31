<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posttest_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('posttest_attempt_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('posttest_question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('selected_option_id')
                ->constrained('posttest_options')
                ->cascadeOnDelete();

            $table->boolean('is_correct')
                ->default(false);

            $table->timestamps();

            $table->unique([
                'posttest_attempt_id',
                'posttest_question_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posttest_answers');
    }
};
