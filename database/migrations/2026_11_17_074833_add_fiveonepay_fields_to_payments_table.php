<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les données renvoyées par FiveOne Pay et le suivi du paiement.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_url', 2048)->nullable()->after('status');
            $table->string('operator', 30)->nullable()->after('payment_url');
            $table->timestamp('expires_at')->nullable()->after('operator');
            $table->timestamp('paid_at')->nullable()->after('expires_at');
            $table->text('error_message')->nullable()->after('paid_at');
            $table->json('payload')->nullable()->after('error_message');
        });
    }

    /**
     * Supprime les données ajoutées.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_url',
                'operator',
                'expires_at',
                'paid_at',
                'error_message',
                'payload',
            ]);
        });
    }
};
