<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->enum('status', ['convoked', 'present', 'absent', 'excused'])->default('convoked');
            $table->enum('lineup', ['starter', 'substitute', 'none'])->default('none');
            $table->string('position_played')->nullable();
            $table->integer('minutes_played')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_players');
    }
};
