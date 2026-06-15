<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('password');
            $table->string('business_name')->nullable()->after('phone');
            $table->text('avatar')->nullable()->after('business_name');
            $table->string('plan')->default('free')->after('avatar');
            $table->string('google_id')->nullable()->unique()->after('plan');
            $table->string('facebook_id')->nullable()->unique()->after('google_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'business_name', 'avatar', 'plan', 'google_id', 'facebook_id']);
        });
    }
};
