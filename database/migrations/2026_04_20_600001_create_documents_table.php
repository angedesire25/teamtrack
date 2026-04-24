<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');

            // Entité propriétaire (joueur, staff, club)
            $table->morphs('documentable');

            // Gestion des versions : toutes les versions d'un même document partagent ce UUID
            $table->char('document_group_id', 36)->index();
            $table->unsignedSmallInteger('version')->default(1);

            $table->enum('document_type', [
                'contrat', 'licence', 'certificat_medical',
                'autorisation_parentale', 'passeport', 'autre',
            ]);
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->date('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Signature électronique
            $table->timestamp('signed_at')->nullable();
            $table->foreignId('signed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('signed_ip', 45)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'documentable_type', 'documentable_id']);
            $table->index(['tenant_id', 'document_type']);
            $table->index(['tenant_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
