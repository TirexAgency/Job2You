<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table cv_parses (parsing de CV par IA).
     */
    public function up(): void
    {
        Schema::create('cv_parses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_path', 500);
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->json('extracted_json')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table cv_parses.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_parses');
    }
};
