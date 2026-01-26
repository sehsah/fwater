<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;

class SyncPermissions extends Command
{
    protected $signature = 'permission:sync';

    protected $description = 'Sync permissions from config into the database (create missing only)';

    public function handle(): int
    {
        $names = config('permissions.sync', []);
        $guardName = config('auth.defaults.guard', 'web');

        if (empty($names)) {
            $this->warn('No permissions defined in config/permissions.php (sync key).');

            return self::FAILURE;
        }

        $created = 0;

        foreach ($names as $name) {
            $permission = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guardName]
            );

            if ($permission->wasRecentlyCreated) {
                $created++;
                $this->line("Created: {$name}");
            }
        }

        $this->info("Synced {$created} new permission(s). ".(count($names) - $created).' already existed.');

        return self::SUCCESS;
    }
}
