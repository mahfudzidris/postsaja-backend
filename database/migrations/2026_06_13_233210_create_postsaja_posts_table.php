<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postsaja_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained('postsaja_businesses')->nullOnDelete();
            $table->bigInteger('staff_chat_id')->nullable();
            $table->text('image_url')->nullable();
            $table->text('ai_caption')->nullable();
            $table->json('platforms_posted')->nullable();
            $table->enum('status', ['processing', 'posted', 'failed'])->default('processing');
            $table->json('analytics')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postsaja_posts');
    }
};
