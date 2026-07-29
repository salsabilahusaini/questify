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
            'badges',
            function (Blueprint $table) {
                $table->id();

                $table->string('name', 100);

                $table->string('slug')
                    ->unique('badge_slug_unique');

                $table->text('description');

                $table->string('icon', 50)
                    ->default('award');

                $table->string('theme', 30)
                    ->default('blue');

                $table->string('criteria_type', 50);

                $table->unsignedInteger('criteria_value')
                    ->default(1);

                $table->unsignedInteger('sequence')
                    ->default(1);

                $table->boolean('is_active')
                    ->default(true);

                $table->timestamps();
            }
        );
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
