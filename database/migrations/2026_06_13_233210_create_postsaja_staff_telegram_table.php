<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postsaja_staff_telegram', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('postsaja_businesses')->cascadeOnDelete();
            $table->bigInteger('telegram_chat_id')->unique();
            $table->string('telegram_username')->nullable();
            $table->string('display_name')->nullable();
            $table->enum('role', ['staff', 'supervisor', 'owner'])->default('staff');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postsaja_staff_telegram');
    }
};
