<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Observers\TransactionObserver;
use App\Services\CategorySuggester;
use App\Services\KeywordCategorySuggester;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategorySuggester::class, KeywordCategorySuggester::class);
    }

    public function boot(): void
    {
        Transaction::observe(TransactionObserver::class);
    }
}
