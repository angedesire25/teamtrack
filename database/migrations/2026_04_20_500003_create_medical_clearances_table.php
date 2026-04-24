<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medical_clearances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['fit', 'unfit', 'conditional'])->default('fit');
            $table->text('reason')->nullable();
            $table->date('effective_date');
            $table->date('review_date')->nullable();
            $table->foreignId('set_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_clearances');
    }
};
