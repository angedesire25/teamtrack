<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('name');
            $table->string('category'); // ballon, cône, chasuble, filet, goal, autre
            $table->enum('condition', ['new', 'good', 'repair', 'out_of_service'])->default('good');
            $table->integer('quantity_total')->default(0);
            $table->integer('quantity_available')->default(0);
            $table->integer('low_stock_threshold')->default(2);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('reference')->nullable(); // référence fournisseur
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_items');
    }
};
