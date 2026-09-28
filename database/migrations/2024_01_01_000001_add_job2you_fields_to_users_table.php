<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les champs spécifiques Job2You à la table users.
     * Vérifie si les colonnes existent déjà pour éviter les erreurs.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // phone existe déjà dans Laravel 13, on vérifie
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 15)->nullable()->unique()->after('email');
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['candidate', 'admin', 'recruiter'])->default('candidate')->after('phone');
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['pending', 'active', 'inactive', 'suspended'])->default('pending')->after('role');
            }
            if (! Schema::hasColumn('users', 'plan')) {
                $table->string('plan', 20)->default('free')->after('status');
            }
            if (! Schema::hasColumn('users', 'sms_quota')) {
                $table->integer('sms_quota')->default(2)->after('plan');
            }
            if (! Schema::hasColumn('users', 'sms_sent')) {
                $table->integer('sms_sent')->default(0)->after('sms_quota');
            }
        });
    }

    /**
     * Supprime les champs Job2You de la table users.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'plan', 'sms_quota', 'sms_sent']);
        });
    }
};
