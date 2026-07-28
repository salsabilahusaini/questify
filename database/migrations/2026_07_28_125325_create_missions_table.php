<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');

            // Menentukan urutan misi pada perjalanan belajar.
            $table->unsignedSmallInteger('sequence')->unique();

            // XP yang diperoleh setelah misi diselesaikan.
            $table->unsignedInteger('xp_reward')->default(100);

            // Hanya misi yang dipublikasikan yang tampil kepada siswa.
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
