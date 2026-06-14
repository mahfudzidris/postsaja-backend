<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postsaja_posts', function (Blueprint $table) {
            // Modify enum to include 'pending' for approval workflow
            $table->string('status', 20)->default('pending')->change();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('postsaja_posts', function (Blueprint $table) {
            $table->dropColumn('approved_by');
            $table->string('status', 20)->default('processing')->change();
        });
    }
};
