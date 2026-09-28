<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table matches (correspondances candidat/offre).
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 4, 2);
            $table->json('details_json')->nullable();

            $table->unique(['user_id', 'offer_id'], 'uq_user_offer');
            $table->index('score', 'idx_score');
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table matches.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
