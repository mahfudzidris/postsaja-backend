<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1 demo user
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@postsaja.com',
            'password' => bcrypt('password'),
            'phone' => '+60 12-345 6789',
            'business_name' => 'PostSaja',
            'plan' => 'pro',
        ]);

        // 5 sample posts matching dashboard samples
        Post::create(['user_id' => $user->id, 'caption' => '🔥 LIMITED OFFER: Fresh baked croissants! Visit us today! #SupportLocal', 'media' => ['https://images.unsplash.com/photo-1606787366850-de6330128bfc?w=400'], 'channels' => ['instagram','telegram'], 'status' => 'published', 'created_at' => now()->subHours(2)]);
        Post::create(['user_id' => $user->id, 'caption' => 'New menu items available at our cafe! Come try our signature lattes ☕', 'media' => ['https://images.unsplash.com/photo-1561758033-d89a9ad46330?w=400'], 'channels' => ['google_business'], 'status' => 'scheduled', 'scheduled_at' => now()->addDay(), 'created_at' => now()->subDay()]);
        Post::create(['user_id' => $user->id, 'caption' => 'Video walkthrough: Our new co-working space at downtown!', 'media' => [], 'channels' => ['tiktok'], 'status' => 'failed', 'created_at' => now()->subDays(2)]);
        Post::create(['user_id' => $user->id, 'caption' => 'Weekend promo: 20% off all items! Use code WEEKEND20', 'media' => ['https://images.unsplash.com/photo-1590004953392-5f89d137c8d5?w=400'], 'channels' => ['instagram','google_business','telegram'], 'status' => 'published', 'created_at' => now()->subDays(3)]);
        Post::create(['user_id' => $user->id, 'caption' => '🔥 NEW ARRIVAL: Limited edition sneakers dropping this Friday!', 'media' => ['https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400'], 'channels' => ['instagram','tiktok','telegram'], 'status' => 'published', 'created_at' => now()->subDays(4)]);

        // 2 sample drafts
        Post::create(['user_id' => $user->id, 'caption' => '🌟 Grand opening this weekend! Come visit our new store at the mall...', 'media' => [], 'channels' => ['instagram','telegram'], 'status' => 'draft', 'created_at' => now()->subMinutes(10)]);
        Post::create(['user_id' => $user->id, 'caption' => 'Weekly special: Buy 1 Free 1 for all coffee drinks! Valid until Sunday...', 'media' => [], 'channels' => ['google_business','tiktok'], 'status' => 'draft', 'created_at' => now()->subDay()]);

        // 4 sample notifications
        Notification::create(['user_id' => $user->id, 'type' => 'post_published', 'title' => 'Post published successfully', 'body' => 'Your croissant promo was posted to Instagram & Telegram', 'created_at' => now()->subHours(2)]);
        Notification::create(['user_id' => $user->id, 'type' => 'post_failed', 'title' => 'Post failed — TikTok', 'body' => 'Video walkthrough couldn\'t be posted. Session expired, please reconnect.', 'created_at' => now()->subHours(3)]);
        Notification::create(['user_id' => $user->id, 'type' => 'post_scheduled', 'title' => 'Post scheduled', 'body' => 'Your new menu item will be posted to Google Business tomorrow at 12:00 PM', 'created_at' => now()->subDay()]);
        Notification::create(['user_id' => $user->id, 'type' => 'tip', 'title' => '💡 Tip: Best posting time', 'body' => 'Posts with images perform 40% better at 12:00 PM. Try scheduling your next post!', 'created_at' => now()->subDays(3)]);
    }
}
