<?php

use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        User::query()->orderBy('id')->chunkById(50, function ($users) {
            foreach ($users as $user) {
                if (HouseholdMember::where('user_id', $user->id)->exists()) {
                    continue;
                }

                $household = Household::create([
                    'invite_code' => Household::generateInviteCode(),
                    'initial_balance' => $user->initial_balance,
                ]);

                HouseholdMember::create([
                    'household_id' => $household->id,
                    'user_id' => $user->id,
                    'role' => HouseholdMember::ROLE_OWNER,
                ]);

                foreach ([
                    'categories',
                    'transactions',
                    'budgets',
                    'savings_goals',
                    'savings_contributions',
                ] as $table) {
                    DB::table($table)
                        ->where('user_id', $user->id)
                        ->update(['household_id' => $household->id]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible data migration: no reliable way to tell a
        // backfilled household from one modified afterward.
    }
};
