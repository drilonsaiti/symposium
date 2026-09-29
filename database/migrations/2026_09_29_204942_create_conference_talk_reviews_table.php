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
        Schema::create('conference_talk_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_talk_id')->constrained('conference_talk')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->string('recommendation');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['conference_talk_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_talk_reviews');
    }
};
