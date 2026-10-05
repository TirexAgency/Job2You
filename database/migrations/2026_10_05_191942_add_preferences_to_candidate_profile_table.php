<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la localisation, la mobilité et les préférences de recherche.
     */
    public function up(): void
    {
        Schema::table('candidate_profile', function (Blueprint $table) {
            $table->string('city', 150)->nullable()->after('location');
            $table->string('region', 150)->nullable()->after('city');
            $table->string('mobility', 50)->default('local')->after('region');
            $table->decimal('latitude', 10, 7)->nullable()->after('mobility');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('contract_type', 50)->nullable()->after('contract_preferences');
            $table->unsignedInteger('desired_salary')->nullable()->after('contract_type');
            $table->json('sectors')->nullable()->after('desired_salary');
            $table->boolean('alerts_enabled')->default(false)->after('sectors');
        });
    }

    public function down(): void
    {
        Schema::table('candidate_profile', function (Blueprint $table) {
            $table->dropColumn([
                'city', 'region', 'mobility', 'latitude', 'longitude',
                'contract_type', 'desired_salary', 'sectors', 'alerts_enabled',
            ]);
        });
    }
};
