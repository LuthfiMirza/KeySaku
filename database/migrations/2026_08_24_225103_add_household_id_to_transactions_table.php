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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('household_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->index(['household_id', 'type']);
            $table->index(['household_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['household_id', 'type']);
            $table->dropIndex(['household_id', 'transaction_date']);
            $table->dropConstrainedForeignId('household_id');
        });
    }
};
