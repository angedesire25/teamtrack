<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jersey_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jersey_id')->constrained('jerseys')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->string('jersey_number')->nullable();
            $table->string('season')->nullable();
            $table->date('assigned_at');
            $table->date('returned_at')->nullable(); // null = toujours attribué
            $table->enum('condition_returned', ['good', 'damaged', 'lost'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jersey_assignments');
    }
};
