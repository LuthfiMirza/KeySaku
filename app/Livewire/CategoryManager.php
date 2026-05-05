<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CategoryManager extends Component
{
    public ?Category $editingCategory = null;

    public string $name = '';

    public string $type = Category::TYPE_EXPENSE;

    public string $color = '#6366f1';

    public string $icon = '💸';

    public function mount(): void
    {
        $this->seedDefaultsIfEmpty();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(Category::types())],
            'color' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $category = $this->editingCategory ?? new Category();
        $category->fill($validated);
        $category->user_id = auth()->id();
        $category->save();

        $this->resetForm();
        session()->flash('category_status', 'Kategori berhasil disimpan.');
    }

    public function edit(int $categoryId): void
    {
        $category = $this->currentUserCategories()->findOrFail($categoryId);

        $this->editingCategory = $category;
        $this->name = $category->name;
        $this->type = $category->type;
        $this->color = $category->color ?? '#6366f1';
        $this->icon = $category->icon ?? '💸';
    }

    public function delete(int $categoryId): void
    {
        $this->currentUserCategories()->findOrFail($categoryId)->delete();

        if ($this->editingCategory?->id === $categoryId) {
            $this->resetForm();
        }

        session()->flash('category_status', 'Kategori berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingCategory', 'name']);
        $this->type = Category::TYPE_EXPENSE;
        $this->color = '#6366f1';
        $this->icon = '💸';
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.category-manager', [
            'expenseCategories' => $this->currentUserCategories()
                ->where('type', Category::TYPE_EXPENSE)
                ->orderBy('name')
                ->get(),
            'incomeCategories' => $this->currentUserCategories()
                ->where('type', Category::TYPE_INCOME)
                ->orderBy('name')
                ->get(),
        ]);
    }

    private function currentUserCategories()
    {
        return auth()->user()->categories();
    }

    private function seedDefaultsIfEmpty(): void
    {
        if ($this->currentUserCategories()->exists()) {
            return;
        }

        $defaults = [
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

        foreach ($defaults as $category) {
            $this->currentUserCategories()->create($category);
        }
    }
}
