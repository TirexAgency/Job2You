<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table de liaison offer_skills.
     */
    public function up(): void
    {
        Schema::create('offer_skills', function (Blueprint $table) {
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->boolean('required')->default(true);
            $table->decimal('weight', 3, 2)->default(1.00);

            $table->primary(['offer_id', 'skill_id']);
        });
    }

    /**
     * Suppression de la table offer_skills.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_skills');
    }
};
