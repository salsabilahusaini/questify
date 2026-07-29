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
            'mission_completions',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('mission_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->unsignedInteger('earned_xp')
                    ->default(0);

                $table->timestamp('completed_at');

                $table->timestamps();

                $table->unique(
                    [
                        'user_id',
                        'mission_id',
                    ],
                    'mission_completion_user_mission_unique'
                );
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_completions');
    }
};
