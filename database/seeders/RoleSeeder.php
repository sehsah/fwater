<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
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
        //assign superadmin to admin user
        $admin = User::find(1);
        if ($admin) {
            $admin->assignRole('superadmin');
        }
    }
}
