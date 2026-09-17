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
        Schema::create('cfp_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->string('type');
            $table->boolean('required')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cfp_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cfp_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conference_talk_id')->constrained('conference_talk')->cascadeOnDelete();
            $table->text('answer');
            $table->timestamps();

            $table->unique(['cfp_question_id', 'conference_talk_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cfp_questions');
    }
};
