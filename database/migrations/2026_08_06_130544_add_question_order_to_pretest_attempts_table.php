<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan urutan soal acak yang tetap
     * untuk setiap pengerjaan Pretest.
     */
    public function up(): void
    {
        Schema::table(
            'pretest_attempts',
            function (Blueprint $table): void {
                $table->json('question_order')
                    ->nullable()
                    ->after('score');
            }
        );
    }

    /**
     * Menghapus kolom urutan soal Pretest.
     */
    public function down(): void
    {
        Schema::table(
            'pretest_attempts',
            function (Blueprint $table): void {
                $table->dropColumn('question_order');
            }
        );
    }
};
