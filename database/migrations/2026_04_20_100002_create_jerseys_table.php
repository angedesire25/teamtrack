<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jerseys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('name');
            $table->enum('type', ['home', 'away', 'training', 'keeper', 'other'])->default('home');
            $table->string('season')->nullable(); // ex: 2024-2025
            $table->string('color')->nullable();
            $table->string('size'); // XS, S, M, L, XL, XXL, XXXL, Enfant...
            $table->integer('quantity_total')->default(0);
            $table->integer('quantity_available')->default(0);
            $table->integer('low_stock_threshold')->default(2);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jerseys');
    }
};
