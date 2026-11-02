<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le suivi des envois SMS : destinataire, message, idempotence et statut fournisseur.
     */
    public function up(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->foreignId('offer_id')->nullable()->change();
            $table->foreignId('subscription_id')->nullable()->change();

            $table->string('quota_source', 20)->nullable()->after('subscription_id');
            $table->string('recipient', 30)->nullable()->after('quota_source');
            $table->text('message')->nullable()->after('recipient');
            $table->string('idempotency_key', 191)->nullable()->unique()->after('message');
            $table->string('provider', 50)->nullable()->after('status');
            $table->string('provider_status', 50)->nullable()->after('provider_reference');
            $table->text('error_message')->nullable()->after('provider_status');
            $table->boolean('quota_consumed')->default(false)->after('error_message');
            $table->unsignedTinyInteger('attempts')->default(0)->after('quota_consumed');
        });
    }

    /**
     * Supprime le suivi ajouté.
     */
    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn([
                'quota_source',
                'recipient',
                'message',
                'idempotency_key',
                'provider',
                'provider_status',
                'error_message',
                'quota_consumed',
                'attempts',
            ]);

            $table->foreignId('offer_id')->nullable(false)->change();
            $table->foreignId('subscription_id')->nullable(false)->change();
        });
    }
};
