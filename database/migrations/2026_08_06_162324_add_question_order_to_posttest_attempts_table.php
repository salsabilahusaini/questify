<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan urutan soal acak yang tetap
     * untuk setiap pengerjaan Posttest.
     */
    public function up(): void
    {
        Schema::table(
            'posttest_attempts',
            function (Blueprint $table): void {
                $table->json('question_order')
                    ->nullable()
                    ->after('score');
            }
        );
    }

    /**
     * Menghapus kolom urutan soal Posttest.
     */
    public function down(): void
    {
        Schema::table(
            'posttest_attempts',
            function (Blueprint $table): void {
                $table->dropColumn('question_order');
            }
        );
    }
};
