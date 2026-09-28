<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table sources (sources de collecte d'offres).
     */
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('base_url', 255)->nullable();
            $table->string('collector_key', 100)->unique();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table sources.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
