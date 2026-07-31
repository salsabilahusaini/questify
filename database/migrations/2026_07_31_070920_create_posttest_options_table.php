<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posttest_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('posttest_question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('option_text');
            $table->unsignedInteger('sequence')->default(1);
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->index([
                'posttest_question_id',
                'sequence',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posttest_options');
    }
};
