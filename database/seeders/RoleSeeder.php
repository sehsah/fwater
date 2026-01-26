<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('permission:sync');

        $guardName = config('auth.defaults.guard', 'web');

        $role = Role::firstOrCreate(
            ['name' => 'superadmin', 'guard_name' => $guardName]
        );

        $role->syncPermissions(Permission::where('guard_name', $guardName)->pluck('name'));
    }
}
