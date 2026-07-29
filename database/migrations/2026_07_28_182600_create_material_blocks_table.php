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
        Schema::create('material_blocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('material_section_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type', 30);

            $table->string('title')
                ->nullable();

            $table->text('content')
                ->nullable();

            $table->string('code_language')
                ->nullable();

            $table->string('media_path')
                ->nullable();

            $table->unsignedInteger('sequence');

            $table->unsignedInteger('minimum_read_seconds')
                ->default(0);

            $table->boolean('is_required')
                ->default(true);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'material_section_id',
                'sequence',
            ]);
        });
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_blocks');
    }
};
