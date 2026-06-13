<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postsaja_businesses', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('owner_name')->nullable();
            $table->string('owner_wa', 50)->nullable();
            $table->string('business_code', 6)->unique();
            $table->boolean('telegram_bot_enabled')->default(true);
            $table->text('google_business_token')->nullable();
            $table->text('fb_token')->nullable();
            $table->text('ig_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postsaja_businesses');
    }
};
