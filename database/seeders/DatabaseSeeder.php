<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Author;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Demo User',
            'email' => 'demo.user@example.com',
            'password' => bcrypt('12345678'),
            'email_verified_at' => now(),
        ]);
        $users = \App\Models\User::factory(10)->create();
        $this->command->info('users created');
        $categories = Category::factory()->count(12)->state(new Sequence(
            ['order' => 1],
            ['order' => 2],
            ['order' => 3],
            ['order' => 4],
            ['order' => 5],
            ['order' => 6],
            ['order' => 7],
            ['order' => 8],
            ['order' => 9],
            ['order' => 10],
            ['order' => 11],
            ['order' => 12],
        ))->create();
        $this->command->info('categories created.');
        Author::factory()->count(13)->has(
            Post::factory()->count(rand(2, 6))
                ->has(
                    Comment::factory()->count(rand(5, 10))
                        ->state(fn () => ['user_id' => $users->random(1)->first()->id]),
                    'comments'
                )->has(
                    Image::factory()->count(rand(2, 3)),
                    'images'
                )
                ->state(fn () => ['category_id' => $categories->random(1)->first()->id]),
            'posts'
        )->create();
        $this->command->info('authors and posts created with comments and images.');
    }
}
