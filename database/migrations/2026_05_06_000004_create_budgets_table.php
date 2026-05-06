<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->unique(['user_id', 'category_id', 'month', 'year']);
            $table->index(['user_id', 'year', 'month']);
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
