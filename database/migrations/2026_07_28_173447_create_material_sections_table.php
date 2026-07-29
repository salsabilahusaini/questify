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
        Schema::create('material_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->string('slug');

            $table->text('description')
                ->nullable();

            $table->unsignedInteger('sequence');

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'mission_id',
                'slug',
            ]);

            $table->unique([
                'mission_id',
                'sequence',
            ]);
        });
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_sections');
    }
};
