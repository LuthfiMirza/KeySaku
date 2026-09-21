<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'preferred_currency',
        'language',
        'telegram_chat_id',
        'telegram_link_token',
    ];

    protected $hidden = [
        'password',
        'preferred_currency',
        'language',
        'remember_token',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function savingsGoals(): HasMany
    {
        return $this->hasMany(SavingsGoal::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function householdMembership(): HasOne
    {
        return $this->hasOne(HouseholdMember::class);
    }

    protected function household(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->householdMembership?->household,
        );
    }

    public function isTelegramLinked(): bool
    {
        return ! empty($this->telegram_chat_id);
    }

    public function generateTelegramLinkToken(): string
    {
        if (empty($this->telegram_link_token)) {
            $this->forceFill([
                'telegram_link_token' => Str::random(32),
            ])->save();
        }

        return $this->telegram_link_token;
    }

    public function linkTelegramChat(string $chatId): void
    {
        $this->forceFill([
            'telegram_chat_id' => $chatId,
            'telegram_link_token' => null,
        ])->save();
    }

    public function unlinkTelegramChat(): void
    {
        $this->forceFill([
            'telegram_chat_id' => null,
            'telegram_link_token' => null,
        ])->save();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
