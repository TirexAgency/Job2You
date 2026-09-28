<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table skills (compétences normalisées).
     */
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('normalized_name', 100)->unique();
            $table->index('normalized_name', 'idx_normalized');
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table skills.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
