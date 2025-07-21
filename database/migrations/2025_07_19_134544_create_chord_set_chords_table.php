<?php

declare(strict_types=1);

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
        Schema::create('chord_set_chords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chord_set_id')->constrained()->cascadeOnDelete();
            $table->integer('position'); // 1-8
            $table->string('tone'); // C, C#, D, etc.
            $table->string('semitone')->nullable(); // major, minor, diminished, augmented
            $table->string('inversion')->nullable(); // root, first, second, third
            $table->boolean('is_blue_note')->default(false);
            $table->timestamps();

            $table->index(['chord_set_id', 'position']);
            $table->unique(['chord_set_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chord_set_chords');
    }
};
