<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'demo@keysaku.test'],
            [
                'name' => 'Demo KeySaku',
                'password' => Hash::make('password'),
            ]
        );

        $categories = $this->seedCategories($user);

        $user->transactions()->forceDelete();
        $user->budgets()->delete();
        $user->savingsGoals()->each(function (SavingsGoal $goal): void {
            $goal->contributions()->delete();
            $goal->delete();
        });

        $this->seedTransactions($user, $categories);
        $this->seedBudgets($user, $categories);
        $this->seedSavings($user);

        $this->command?->info('Demo data ready: demo@keysaku.test / password');
    }

    private function seedCategories(User $user): array
    {
        $expenseCategories = [
            'Makanan' => ['color' => '#f97316', 'icon' => '🍜'],
            'Transportasi' => ['color' => '#06b6d4', 'icon' => '🚌'],
            'Belanja Dapur' => ['color' => '#22c55e', 'icon' => '🛒'],
            'Tagihan' => ['color' => '#6366f1', 'icon' => '💡'],
            'Kesehatan' => ['color' => '#ef4444', 'icon' => '💊'],
            'Pendidikan' => ['color' => '#8b5cf6', 'icon' => '📚'],
            'Hiburan' => ['color' => '#ec4899', 'icon' => '🎬'],
            'Lainnya' => ['color' => '#64748b', 'icon' => '💸'],
        ];

        $incomeCategories = [
            'Gaji' => ['color' => '#10b981', 'icon' => '💼'],
            'Bonus' => ['color' => '#84cc16', 'icon' => '🎁'],
            'Usaha' => ['color' => '#14b8a6', 'icon' => '🏪'],
            'Hadiah' => ['color' => '#f59e0b', 'icon' => '🎉'],
            'Lainnya' => ['color' => '#64748b', 'icon' => '💰'],
        ];

        $categories = [];

        foreach ($expenseCategories as $name => $meta) {
            $categories['expense'][$name] = Category::updateOrCreate(
                ['user_id' => $user->id, 'name' => $name, 'type' => Category::TYPE_EXPENSE],
                ['color' => $meta['color'], 'icon' => $meta['icon']]
            );
        }

        foreach ($incomeCategories as $name => $meta) {
            $categories['income'][$name] = Category::updateOrCreate(
                ['user_id' => $user->id, 'name' => $name, 'type' => Category::TYPE_INCOME],
                ['color' => $meta['color'], 'icon' => $meta['icon']]
            );
        }

        return $categories;
    }

    private function seedTransactions(User $user, array $categories): void
    {
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonthNoOverflow()->startOfMonth();

        $transactions = [
            [$categories['income']['Gaji'], Transaction::TYPE_INCOME, 8500000, $currentMonth->copy()->addDays(1), 'Gaji bulanan', Transaction::SOURCE_MANUAL, null],
            [$categories['income']['Usaha'], Transaction::TYPE_INCOME, 1250000, $currentMonth->copy()->addDays(7), 'Penjualan kecil', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Makanan'], Transaction::TYPE_EXPENSE, 850000, $currentMonth->copy()->addDays(2), 'Belanja makan keluarga', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Makanan'], Transaction::TYPE_EXPENSE, 275000, $currentMonth->copy()->addDays(10), 'Makan akhir pekan', Transaction::SOURCE_VOICE, 'catat pengeluaran makanan dua ratus tujuh puluh lima ribu'],
            [$categories['expense']['Transportasi'], Transaction::TYPE_EXPENSE, 620000, $currentMonth->copy()->addDays(4), 'Bensin dan parkir', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Belanja Dapur'], Transaction::TYPE_EXPENSE, 1250000, $currentMonth->copy()->addDays(6), 'Stok dapur mingguan', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Tagihan'], Transaction::TYPE_EXPENSE, 1450000, $currentMonth->copy()->addDays(8), 'Listrik, air, internet', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Kesehatan'], Transaction::TYPE_EXPENSE, 180000, $currentMonth->copy()->addDays(12), 'Vitamin anak', Transaction::SOURCE_VOICE, 'pengeluaran kesehatan seratus delapan puluh ribu untuk vitamin anak'],
            [$categories['expense']['Pendidikan'], Transaction::TYPE_EXPENSE, 750000, $currentMonth->copy()->addDays(14), 'Buku dan les', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Hiburan'], Transaction::TYPE_EXPENSE, 320000, $currentMonth->copy()->addDays(16), 'Nonton keluarga', Transaction::SOURCE_VOICE, 'catat hiburan tiga ratus dua puluh ribu nonton keluarga'],
            [$categories['income']['Gaji'], Transaction::TYPE_INCOME, 8500000, $previousMonth->copy()->addDays(1), 'Gaji bulan lalu', Transaction::SOURCE_MANUAL, null],
            [$categories['income']['Bonus'], Transaction::TYPE_INCOME, 1000000, $previousMonth->copy()->addDays(5), 'Bonus proyek', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Makanan'], Transaction::TYPE_EXPENSE, 2100000, $previousMonth->copy()->addDays(3), 'Makan dan jajanan bulan lalu', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Transportasi'], Transaction::TYPE_EXPENSE, 900000, $previousMonth->copy()->addDays(7), 'Transportasi bulan lalu', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Belanja Dapur'], Transaction::TYPE_EXPENSE, 1850000, $previousMonth->copy()->addDays(9), 'Belanja bulanan', Transaction::SOURCE_VOICE, 'belanja dapur satu juta delapan ratus lima puluh ribu'],
            [$categories['expense']['Tagihan'], Transaction::TYPE_EXPENSE, 1380000, $previousMonth->copy()->addDays(11), 'Tagihan rumah', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Kesehatan'], Transaction::TYPE_EXPENSE, 350000, $previousMonth->copy()->addDays(15), 'Obat dan konsultasi', Transaction::SOURCE_MANUAL, null],
            [$categories['expense']['Pendidikan'], Transaction::TYPE_EXPENSE, 650000, $previousMonth->copy()->addDays(18), 'Iuran sekolah', Transaction::SOURCE_MANUAL, null],
        ];

        foreach ($transactions as [$category, $type, $amount, $date, $note, $source, $rawVoiceText]) {
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'type' => $type,
                'amount' => $amount,
                'transaction_date' => $date->toDateString(),
                'note' => $note,
                'source' => $source,
                'raw_voice_text' => $rawVoiceText,
            ]);
        }
    }

    private function seedBudgets(User $user, array $categories): void
    {
        $currentMonth = now();
        $previousMonth = now()->subMonthNoOverflow();

        $currentBudgets = [
            'Makanan' => 1300000,
            'Transportasi' => 550000,
            'Belanja Dapur' => 1800000,
            'Tagihan' => 1500000,
            'Kesehatan' => 500000,
            'Pendidikan' => 900000,
        ];

        $previousBudgets = [
            'Makanan' => 2200000,
            'Transportasi' => 1000000,
            'Belanja Dapur' => 2000000,
            'Tagihan' => 1500000,
            'Kesehatan' => 450000,
            'Pendidikan' => 800000,
        ];

        $this->createBudgets($user, $categories['expense'], $currentBudgets, $currentMonth);
        $this->createBudgets($user, $categories['expense'], $previousBudgets, $previousMonth);
    }

    private function createBudgets(User $user, array $categories, array $budgets, Carbon $month): void
    {
        foreach ($budgets as $categoryName => $amount) {
            Budget::create([
                'user_id' => $user->id,
                'category_id' => $categories[$categoryName]->id,
                'month' => $month->month,
                'year' => $month->year,
                'amount' => $amount,
            ]);
        }
    }

    private function seedSavings(User $user): void
    {
        $goals = [
            [
                'name' => 'Dana Darurat',
                'target_amount' => 15000000,
                'deadline' => now()->addMonths(8)->toDateString(),
                'contributions' => [
                    [2500000, now()->subMonths(2)->toDateString(), 'Saldo awal dana darurat'],
                    [1500000, now()->subMonth()->toDateString(), 'Setoran dari bonus'],
                    [1000000, now()->subDays(5)->toDateString(), 'Setoran rutin bulanan'],
                ],
            ],
            [
                'name' => 'Liburan Keluarga',
                'target_amount' => 8000000,
                'deadline' => now()->addMonths(5)->toDateString(),
                'contributions' => [
                    [2000000, now()->subMonth()->addDays(3)->toDateString(), 'Tabungan tiket'],
                    [1500000, now()->subDays(8)->toDateString(), 'Tambahan akomodasi'],
                ],
            ],
            [
                'name' => 'Laptop Anak',
                'target_amount' => 6000000,
                'deadline' => now()->addMonths(3)->toDateString(),
                'contributions' => [
                    [2500000, now()->subMonth()->addDays(10)->toDateString(), 'Mulai menabung laptop'],
                    [2500000, now()->subDays(2)->toDateString(), 'Hampir tercapai'],
                ],
            ],
        ];

        foreach ($goals as $goalData) {
            $currentAmount = collect($goalData['contributions'])->sum(fn ($contribution) => $contribution[0]);

            $goal = SavingsGoal::create([
                'user_id' => $user->id,
                'name' => $goalData['name'],
                'target_amount' => $goalData['target_amount'],
                'current_amount' => $currentAmount,
                'deadline' => $goalData['deadline'],
                'status' => SavingsGoal::STATUS_ACTIVE,
            ]);

            foreach ($goalData['contributions'] as [$amount, $date, $note]) {
                SavingsContribution::create([
                    'savings_goal_id' => $goal->id,
                    'amount' => $amount,
                    'contribution_date' => $date,
                    'note' => $note,
                ]);
            }
        }
    }
}
