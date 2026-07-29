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
            'material_block_progresses',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('material_block_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->timestamp('first_opened_at')
                    ->nullable();

                $table->timestamp('last_activity_at')
                    ->nullable();

                $table->unsignedInteger('total_read_seconds')
                    ->default(0);

                $table->unsignedInteger('visit_count')
                    ->default(0);

                $table->boolean('is_completed')
                    ->default(false);

                $table->timestamp('completed_at')
                    ->nullable();

                $table->timestamps();

                $table->unique([
                    'user_id',
                    'material_block_id',
                ]);
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_block_progresses');
    }
};
