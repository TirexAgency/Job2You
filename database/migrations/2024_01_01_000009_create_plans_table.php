<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table plans (abonnements).
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->integer('sms_quota')->default(0);
            $table->boolean('cv_parsing_enabled')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table plans.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
