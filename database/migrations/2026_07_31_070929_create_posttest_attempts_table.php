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
        Schema::create('posttest_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('mission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status')
                ->default('in_progress');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->unsignedInteger('total_questions')
                ->default(0);

            $table->unsignedInteger('correct_answers')
                ->default(0);

            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();

            $table->unique([
                'user_id',
                'mission_id',
            ]);

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posttest_attempts');
    }
};
