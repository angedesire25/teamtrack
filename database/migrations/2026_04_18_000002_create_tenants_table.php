<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 10)->default('CI');
            $table->string('logo')->nullable();
            $table->string('primary_color', 20)->default('#1E3A5F');
            $table->string('secondary_color', 20)->default('#2E75B6');
            $table->enum('status', ['active', 'suspended', 'trial', 'cancelled'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
