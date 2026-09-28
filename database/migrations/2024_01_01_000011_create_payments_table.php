<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table payments (paiements via FiveOnePay).
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->string('provider', 50);
            $table->string('provider_reference', 50)->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->text('payload_reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table payments.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
