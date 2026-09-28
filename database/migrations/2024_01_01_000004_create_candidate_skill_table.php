<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table de liaison candidate_skill.
     */
    public function up(): void
    {
        Schema::create('candidate_skill', function (Blueprint $table) {
            $table->foreignId('candidate_profile_id')->constrained('candidate_profile')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('level')->default(1);
            $table->decimal('weight', 3, 2)->default(1.00);

            $table->primary(['candidate_profile_id', 'skill_id']);
        });
    }

    /**
     * Suppression de la table candidate_skill.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_skill');
    }
};
