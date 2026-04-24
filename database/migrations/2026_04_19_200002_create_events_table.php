<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->enum('type', ['match', 'training', 'meeting', 'travel'])->default('training');
            $table->string('title');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();

            // Récurrence
            $table->boolean('is_recurring')->default(false);
            $table->date('recurrence_until')->nullable();
            $table->foreignId('parent_event_id')->nullable()->constrained('events')->cascadeOnDelete();

            // Champs spécifiques aux matchs
            $table->string('competition')->nullable();
            $table->string('opponent')->nullable();
            $table->enum('home_away', ['home', 'away', 'neutral'])->nullable();
            $table->integer('score_home')->nullable();
            $table->integer('score_away')->nullable();
            $table->text('match_report')->nullable();
            $table->boolean('convocations_sent')->default(false);

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
