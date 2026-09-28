<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table subscriptions (abonnements des utilisateurs).
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->integer('sms_remaining')->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table subscriptions.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
