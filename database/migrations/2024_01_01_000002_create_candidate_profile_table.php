<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table candidate_profile (profil candidat manuel).
     */
    public function up(): void
    {
        Schema::create('candidate_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('desired_jobs', 50)->nullable();
            $table->string('location', 150)->nullable();
            $table->enum('experience_level', ['junior', 'mid', 'senior'])->default('junior');
            $table->string('education_level', 100)->nullable();
            $table->string('contract_preferences', 255)->nullable();
            $table->string('profile_source', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table candidate_profile.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_profile');
    }
};
