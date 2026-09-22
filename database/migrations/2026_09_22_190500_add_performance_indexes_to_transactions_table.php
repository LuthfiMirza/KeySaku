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
            $table->index(['household_id', 'type', 'transaction_date'], 'transactions_household_type_date_idx');
            $table->index(['household_id', 'category_id', 'transaction_date'], 'transactions_household_category_date_idx');
            $table->index(['household_id', 'account_id'], 'transactions_household_account_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_household_type_date_idx');
            $table->dropIndex('transactions_household_category_date_idx');
            $table->dropIndex('transactions_household_account_idx');
        });
    }
};
