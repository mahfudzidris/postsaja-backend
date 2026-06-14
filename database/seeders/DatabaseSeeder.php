<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            PostsajaDemoSeeder::class,
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Mahfudz Idris',
            'email' => 'admin@postsaja.com',
        ]);
        $admin->assignRole('admin');

        // Create demo owner
        $owner = User::factory()->create([
            'name' => 'Demo Owner',
            'email' => 'owner@postsaja.com',
        ]);
        $owner->assignRole('owner');
    }
}
