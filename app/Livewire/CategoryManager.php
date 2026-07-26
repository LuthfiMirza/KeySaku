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
        $category = $this->currentUserCategories()->findOrFail($categoryId);

        $transactionCount = $category->transactions()->count();

        if ($transactionCount > 0) {
            session()->flash(
                'category_error',
                "Kategori ini masih dipakai oleh {$transactionCount} transaksi. Hapus atau pindahkan transaksi tersebut lebih dulu."
            );
            return;
        }

        $category->delete();

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

}
