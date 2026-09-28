<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table offers (offres d'emploi collectées).
     */
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained()->onDelete('cascade');
            $table->string('external_id', 100);
            $table->string('title', 200);
            $table->string('company', 150)->nullable();
            $table->string('location', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('contract_type', 50)->nullable();
            $table->string('source_url', 500)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('fingerprint', 64)->nullable();
            $table->enum('status', ['active', 'expired', 'archived'])->default('active');

            $table->unique(['source_id', 'external_id'], 'uq_source_external');
            $table->index('fingerprint', 'idx_fingerprint');
            $table->index('status', 'idx_status');
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table offers.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
