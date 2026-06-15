<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('postsaja_business_user');
        Schema::dropIfExists('postsaja_social_accounts');
        Schema::dropIfExists('postsaja_staff_telegram');
        Schema::dropIfExists('postsaja_posts');
        Schema::dropIfExists('postsaja_businesses');
    }

    public function down(): void
    {
        // Nothing to restore — these were old tables being cleaned up
    }
};
