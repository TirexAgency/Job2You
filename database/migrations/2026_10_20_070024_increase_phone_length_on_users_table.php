<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer l'index unique d'abord
            $table->dropUnique('users_phone_unique');
            // Changer la taille
            $table->string('phone', 20)->nullable()->change();
            // Remettre l'index unique
            $table->unique('phone', 'users_phone_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_phone_unique');
            $table->string('phone', 15)->nullable()->change();
            $table->unique('phone', 'users_phone_unique');
        });
    }
};