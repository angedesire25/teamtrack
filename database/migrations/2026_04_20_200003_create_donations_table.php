<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('donation_campaigns')->nullOnDelete();
            $table->foreignId('donor_id')->nullable()->constrained('donors')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('eur');
            $table->enum('frequency', ['one_time', 'monthly', 'annual'])->default('one_time');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('stripe_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->text('message')->nullable();
            $table->string('receipt_number')->nullable()->unique();
            $table->timestamp('receipt_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
