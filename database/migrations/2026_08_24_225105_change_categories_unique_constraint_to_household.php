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
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'type', 'name']);
            $table->unique(['household_id', 'type', 'name']);
            $table->index(['household_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['household_id', 'type']);
            $table->dropUnique(['household_id', 'type', 'name']);
            $table->unique(['user_id', 'type', 'name']);
        });
    }
};
