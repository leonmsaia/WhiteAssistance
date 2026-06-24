<?php

namespace Database\Seeders;

use App\Modules\AccessControl\Domain\RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (RoleName::all() as $role) {
            Role::findOrCreate($role, 'web');
        }
    }
}
