<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // ─── Permissions ───
        $permissions = [
            'view-dashboard',
            'manage-businesses',
            'manage-staff',
            'view-posts',
            'approve-posts',
            'connect-google-business',
            'connect-whatsapp',
            'manage-settings',
        ];

        foreach ($permissions as $p) {
            Permission::findOrCreate($p);
        }

        // ─── Roles ───
        $owner = Role::findOrCreate('owner');
        $owner->givePermissionTo([
            'view-dashboard',
            'manage-businesses',
            'manage-staff',
            'view-posts',
            'approve-posts',
            'connect-google-business',
            'connect-whatsapp',
            'manage-settings',
        ]);

        $staff = Role::findOrCreate('staff');
        $staff->givePermissionTo([
            'view-posts',
        ]);

        $admin = Role::findOrCreate('admin');
        $admin->givePermissionTo(Permission::all());
    }
}
