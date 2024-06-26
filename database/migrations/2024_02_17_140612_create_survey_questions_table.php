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
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();

            $table->string('type');
            $table->string('image')->nullable();
            $table->string('question');
            $table->longText('description')->nullable();
            $table->longText('data')->nullable();

            $table->unsignedBigInteger('survey_id')->nullable();

            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
