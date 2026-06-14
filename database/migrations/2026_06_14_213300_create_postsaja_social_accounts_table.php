<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postsaja_social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('postsaja_businesses')->cascadeOnDelete();
            $table->string('platform');     // google_business, facebook, instagram, whatsapp, tiktok
            $table->string('label')->nullable(); // e.g. "Main Profile", "Store #2"
            $table->text('token')->nullable();
            $table->json('meta')->nullable();     // provider, api_key, phone_number_id, etc
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['business_id', 'platform', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postsaja_social_accounts');
    }
};
