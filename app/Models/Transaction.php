<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_VOICE = 'voice';

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'amount',
        'transaction_date',
        'note',
        'source',
        'raw_voice_text',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function types(): array
    {
        return [self::TYPE_INCOME, self::TYPE_EXPENSE];
    }

    public static function sources(): array
    {
        return [self::SOURCE_MANUAL, self::SOURCE_VOICE];
    }
}
