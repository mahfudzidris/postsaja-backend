<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postsaja_business_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('postsaja_businesses')->cascadeOnDelete();
            $table->string('role'); // owner, supervisor, staff
            $table->timestamps();

            $table->unique(['user_id', 'business_id', 'role']);
        });

        // Migrate existing owner_name data to pivot table
        DB::statement("
            INSERT INTO postsaja_business_user (user_id, business_id, role, created_at, updated_at)
            SELECT u.id, b.id, 'owner', NOW(), NOW()
            FROM postsaja_businesses b
            JOIN users u ON u.name = b.owner_name
            WHERE NOT EXISTS (
                SELECT 1 FROM postsaja_business_user pb
                WHERE pb.user_id = u.id AND pb.business_id = b.id AND pb.role = 'owner'
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('postsaja_business_user');
    }
};
