<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('injuries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->enum('injury_type', ['musculaire', 'osseuse', 'ligamentaire', 'articulaire', 'tendon', 'autre']);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('estimated_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->text('treatment')->nullable();
            $table->enum('status', ['active', 'recovering', 'recovered'])->default('active');
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'player_id']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('injuries');
    }
};
