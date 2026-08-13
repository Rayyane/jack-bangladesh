<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdminRole = Role::findOrCreate('super_admin');
        $editorRole = Role::findOrCreate('editor');

        $admin = User::query()->updateOrCreate(
            ['email' => 'superadmin@jackbangladesh.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('superadminjack@1234#'),
            ],
        );
        $admin->syncRoles([$superAdminRole]);

        $editor = User::query()->updateOrCreate(
            ['email' => 'editor@jackbangladesh.com'],
            [
                'name' => 'Editor',
                'password' => Hash::make('editor@1234#'),
            ],
        );
        $editor->syncRoles([$editorRole]);
    }
}
