<?php

use App\Models\Category;

return [
    ['name' => 'Makanan', 'type' => Category::TYPE_EXPENSE, 'color' => '#f97316', 'icon' => '🍜'],
    ['name' => 'Transportasi', 'type' => Category::TYPE_EXPENSE, 'color' => '#3b82f6', 'icon' => '🚗'],
    ['name' => 'Belanja Dapur', 'type' => Category::TYPE_EXPENSE, 'color' => '#22c55e', 'icon' => '🛒'],
    ['name' => 'Tagihan', 'type' => Category::TYPE_EXPENSE, 'color' => '#ef4444', 'icon' => '🧾'],
    ['name' => 'Kesehatan', 'type' => Category::TYPE_EXPENSE, 'color' => '#14b8a6', 'icon' => '🏥'],
    ['name' => 'Pendidikan', 'type' => Category::TYPE_EXPENSE, 'color' => '#8b5cf6', 'icon' => '🎓'],
    ['name' => 'Hiburan', 'type' => Category::TYPE_EXPENSE, 'color' => '#ec4899', 'icon' => '🎮'],
    ['name' => 'Lainnya', 'type' => Category::TYPE_EXPENSE, 'color' => '#64748b', 'icon' => '📦'],
    ['name' => 'Gaji', 'type' => Category::TYPE_INCOME, 'color' => '#10b981', 'icon' => '💼'],
    ['name' => 'Bonus', 'type' => Category::TYPE_INCOME, 'color' => '#f59e0b', 'icon' => '🎁'],
    ['name' => 'Usaha', 'type' => Category::TYPE_INCOME, 'color' => '#06b6d4', 'icon' => '🏪'],
    ['name' => 'Hadiah', 'type' => Category::TYPE_INCOME, 'color' => '#d946ef', 'icon' => '🎉'],
    ['name' => 'Lainnya', 'type' => Category::TYPE_INCOME, 'color' => '#64748b', 'icon' => '💰'],
];
