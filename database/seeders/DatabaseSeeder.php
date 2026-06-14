<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PostsajaBusiness;
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
            'name' => 'Admin PostSaja',
            'email' => 'admin@postsaja.com',
        ]);
        $admin->assignRole('admin');

        // Ensure admin has a business linked via pivot
        $adminBiz = PostsajaBusiness::firstOrCreate(
            ['business_code' => 'ADMIN01'],
            ['business_name' => 'Demo Admin Business', 'created_at' => now(), 'updated_at' => now()]
        );
        $admin->businesses()->syncWithoutDetaching([
            $adminBiz->id => ['role' => 'owner'],
        ]);

        // Create demo owner
        $owner = User::factory()->create([
            'name' => 'Demo Owner',
            'email' => 'owner@postsaja.com',
        ]);
        $owner->assignRole('owner');

        // Ensure owner has a business linked via pivot
        $ownerBiz = PostsajaBusiness::firstOrCreate(
            ['business_code' => 'MAKAN'],
            ['business_name' => 'Kedai Makan Demo', 'created_at' => now(), 'updated_at' => now()]
        );
        $owner->businesses()->syncWithoutDetaching([
            $ownerBiz->id => ['role' => 'owner'],
        ]);
    }
}
