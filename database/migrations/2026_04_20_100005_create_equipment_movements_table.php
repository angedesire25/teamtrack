<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_item_id')->constrained('equipment_items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['in', 'out', 'return', 'adjustment'])->default('out');
            $table->integer('quantity');
            $table->string('reason')->nullable(); // ex: entraînement U17, achat fournisseur...
            $table->date('expected_return_at')->nullable();
            $table->date('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_movements');
    }
};
