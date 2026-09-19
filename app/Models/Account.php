<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;

    public const TYPE_CASH       = 'cash';
    public const TYPE_BANK       = 'bank';
    public const TYPE_EWALLET    = 'ewallet';
    public const TYPE_INVESTMENT = 'investment';

    protected $fillable = [
        'household_id',
        'user_id',
        'name',
        'type',
        'balance',
        'color',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'balance'   => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function types(): array
    {
        return [self::TYPE_CASH, self::TYPE_BANK, self::TYPE_EWALLET, self::TYPE_INVESTMENT];
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            self::TYPE_CASH       => 'Tunai',
            self::TYPE_BANK       => 'Bank',
            self::TYPE_EWALLET    => 'Dompet Digital',
            self::TYPE_INVESTMENT => 'Investasi',
            default               => $type,
        };
    }

    public function typeLabelAttribute(): string
    {
        return self::typeLabel($this->type);
    }
}
