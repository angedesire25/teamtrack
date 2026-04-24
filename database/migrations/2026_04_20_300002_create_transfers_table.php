<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            $table->string('player_name')->nullable(); // nom libre pour profils entrants sans joueur lié
            $table->enum('direction', ['outgoing', 'incoming']);
            $table->enum('type', ['permanent', 'loan'])->default('permanent');
            $table->enum('status', ['listed', 'negotiating', 'offer_received', 'agreed', 'finalized', 'cancelled'])->default('listed');
            $table->string('counterpart_club')->nullable();
            $table->string('counterpart_contact')->nullable();
            $table->unsignedBigInteger('asking_price')->nullable();
            $table->unsignedBigInteger('agreed_fee')->nullable();
            $table->unsignedInteger('loan_duration_months')->nullable();
            $table->date('loan_start_date')->nullable();
            $table->date('loan_end_date')->nullable();
            $table->json('clauses')->nullable();
            $table->text('notes')->nullable();
            // Champs spécifiques aux profils recherchés (entrants sans joueur identifié)
            $table->string('search_position')->nullable();
            $table->unsignedInteger('search_age_min')->nullable();
            $table->unsignedInteger('search_age_max')->nullable();
            $table->unsignedBigInteger('search_budget_max')->nullable();
            $table->text('search_criteria')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
