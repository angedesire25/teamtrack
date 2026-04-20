<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->unsignedInteger('amount'); // en FCFA
            $table->enum('status', ['paid', 'pending', 'failed'])->default('pending');
            $table->string('method')->nullable(); // mobile_money, virement, especes, stripe
            $table->text('note')->nullable();
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
