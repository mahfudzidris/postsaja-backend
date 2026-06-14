<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Link any existing users to businesses by matching owner_name
        DB::statement("
            INSERT IGNORE INTO postsaja_business_user (user_id, business_id, role, created_at, updated_at)
            SELECT u.id, b.id, 'owner', NOW(), NOW()
            FROM postsaja_businesses b
            JOIN users u ON u.name = b.owner_name
        ");
    }

    public function down(): void
    {
        // Data migration, no rollback needed
    }
};
