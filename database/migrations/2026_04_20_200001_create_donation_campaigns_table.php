<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->decimal('goal_amount', 12, 2)->nullable();
            $table->decimal('collected_amount', 12, 2)->default(0);
            $table->json('suggested_amounts')->nullable(); // [5000, 10000, 25000, 50000]
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_recurring')->default(true);
            $table->boolean('allow_anonymous')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_campaigns');
    }
};
