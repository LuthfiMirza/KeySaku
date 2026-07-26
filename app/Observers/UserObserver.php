<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        $now = now();
        $defaults = config('default-categories', []);

        if ($defaults === []) {
            return;
        }

        $rows = array_map(fn (array $entry) => [
            'user_id' => $user->id,
            'name' => $entry['name'],
            'type' => $entry['type'],
            'color' => $entry['color'] ?? null,
            'icon' => $entry['icon'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $defaults);

        $user->categories()->insert($rows);
    }
}
