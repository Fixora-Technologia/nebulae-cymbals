<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Remove any remaining unused tables from the public pages
     */
    public function up(): void
    {
        // Drop any remaining tables related to public pages
        Schema::dropIfExists('calendars');
        Schema::dropIfExists('dpk_apindo');
        Schema::dropIfExists('histories');
        Schema::dropIfExists('visions');
        Schema::dropIfExists('missions');
        Schema::dropIfExists('sectors');
    }

    /**
     * Reverse the migrations.
     * 
     * Left empty to avoid recreating dropped tables
     */
    public function down(): void
    {
        // We don't want to recreate these tables on rollback
    }
};
