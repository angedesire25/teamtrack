<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // null = super administrateur de la plateforme
            $table->foreignId('tenant_id')->nullable()->after('id')
                ->constrained()->nullOnDelete();
            $table->boolean('is_super_admin')->default(false)->after('remember_token');
            $table->boolean('is_active')->default(true)->after('is_super_admin');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'is_super_admin', 'is_active', 'last_login_at']);
        });
    }
};
