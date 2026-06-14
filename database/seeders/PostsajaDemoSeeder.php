<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsajaDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('postsaja_businesses')->insertOrIgnore([
            [
                'id' => 1,
                'business_name' => 'Bengkel Demo Khamis',
                'business_code' => 'BENGKEL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'business_name' => 'Kedai Makan Demo',
                'business_code' => 'MAKAN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
