<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to drop all unused tables.
     */
    public function up(): void
    {
        // Drop news table
        if (Schema::hasTable('news')) {
            Schema::dropIfExists('news');
        }

        // Drop activities table
        if (Schema::hasTable('activities')) {
            Schema::dropIfExists('activities');
        }

        // Drop members table
        if (Schema::hasTable('members')) {
            Schema::dropIfExists('members');
        }

        // Drop regulations table
        if (Schema::hasTable('regulations')) {
            Schema::dropIfExists('regulations');
        }

        // Drop management structure tables
        if (Schema::hasTable('management')) {
            Schema::dropIfExists('management');
        }

        if (Schema::hasTable('sectors')) {
            Schema::dropIfExists('sectors');
        }

        if (Schema::hasTable('organizational_positions')) {
            Schema::dropIfExists('organizational_positions');
        }

        if (Schema::hasTable('councils')) {
            Schema::dropIfExists('councils');
        }

        // Drop gallery table
        if (Schema::hasTable('galeris')) {
            Schema::dropIfExists('galeris');
        }

        // Drop messages table
        if (Schema::hasTable('pesans')) {
            Schema::dropIfExists('pesans');
        }

        // Drop testimonials table
        if (Schema::hasTable('testimonis')) {
            Schema::dropIfExists('testimonis');
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Note: This down method is intentionally left empty as we don't want to recreate
     * the tables if the migration is rolled back. This is a cleanup migration.
     */
    public function down(): void
    {
        // Intentionally left empty
    }
};
