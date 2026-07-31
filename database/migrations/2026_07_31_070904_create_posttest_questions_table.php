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
        Schema::create('posttest_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('question_text');
            $table->unsignedInteger('sequence')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index([
                'mission_id',
                'is_active',
                'sequence',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posttest_questions');
    }
};
