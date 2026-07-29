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
            'final_challenge_attempts',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('mission_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('final_challenge_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('status', 20)
                    ->default('in_progress');

                $table->longText('answer_code')
                    ->nullable();

                $table->text('answer_output')
                    ->nullable();

                $table->longText('answer_explanation')
                    ->nullable();

                $table->timestamp('started_at');

                $table->timestamp('completed_at')
                    ->nullable();

                $table->decimal('score', 5, 2)
                    ->nullable();

                $table->unsignedInteger('earned_points')
                    ->default(0);

                $table->text('feedback')
                    ->nullable();

                $table->timestamps();

                $table->unique(
                    [
                        'user_id',
                        'final_challenge_id',
                    ],
                    'final_attempt_user_challenge_unique'
                );
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_challenge_attempts');
    }
};
