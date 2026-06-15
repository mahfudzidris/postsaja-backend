<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // free, basic, pro
            $table->decimal('price', 8, 2)->default(0);
            $table->string('currency', 3)->default('MYR');
            $table->integer('max_channels')->default(1);
            $table->integer('max_posts_per_month')->default(10);
            $table->json('features')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
