<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('savings_contributions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('savings_goal_id')->constrained()->cascadeOnDelete();
        });

        DB::statement('
            UPDATE savings_contributions
            SET user_id = (
                SELECT user_id FROM savings_goals WHERE savings_goals.id = savings_contributions.savings_goal_id
            )
            WHERE user_id IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_contributions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
