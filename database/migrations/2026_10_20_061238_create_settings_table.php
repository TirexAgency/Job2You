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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();           // Clé de configuration (ex: matching.skills_weight)
            $table->string('label');                    // Label lisible
            $table->text('value')->nullable();          // Valeur stockée (JSON ou string)
            $table->string('type')->default('string');  // string, integer, decimal, boolean, json
            $table->text('description')->nullable();    // Description métier
            $table->string('group')->default('general'); // Groupe: matching, sms, scraping, etc.
            $table->boolean('editable')->default(true); // Modifiable en admin
            $table->timestamps();

            $table->index(['group', 'key'], 'idx_settings_group_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};