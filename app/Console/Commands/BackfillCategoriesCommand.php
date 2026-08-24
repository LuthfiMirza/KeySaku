<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BackfillCategoriesCommand extends Command
{
    protected $signature = 'categories:backfill {--dry-run : Report affected users without changing data}';

    protected $description = 'Seed default categories for users that currently have zero categories';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $defaults = config('default-categories', []);

        if ($defaults === []) {
            $this->warn('No default categories configured. Nothing to backfill.');
            return self::SUCCESS;
        }

        $users = User::query()->doesntHave('categories')->get();

        if ($users->isEmpty()) {
            $this->info('Every user already has at least one category. Nothing to do.');
            return self::SUCCESS;
        }

        $now = now();
        $processed = 0;

        foreach ($users as $user) {
            if ($dryRun) {
                $this->line("[dry-run] would seed 13 categories for user #{$user->id} ({$user->email})");
                $processed++;
                continue;
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
            $processed++;
        }

        $verb = $dryRun ? 'Would process' : 'Processed';
        $this->info("{$verb} {$processed} user(s).");

        return self::SUCCESS;
    }
}
