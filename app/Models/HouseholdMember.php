<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdMember extends Model
{
    use HasFactory;

    public const ROLE_OWNER = 'owner';
    public const ROLE_MEMBER = 'member';

    protected $fillable = [
        'household_id',
        'user_id',
        'role',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function roles(): array
    {
        return [self::ROLE_OWNER, self::ROLE_MEMBER];
    }
}
