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
            'final_challenges',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('mission_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('title');

                $table->text('scenario');

                $table->text('instructions')
                    ->nullable();

                $table->longText('starter_code')
                    ->nullable();

                $table->text('expected_output')
                    ->nullable();

                $table->unsignedInteger('max_points')
                    ->default(100);

                $table->boolean('is_active')
                    ->default(true);

                $table->timestamps();

                $table->unique(
                    'mission_id',
                    'final_challenge_mission_unique'
                );
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_challenges');
    }
};
