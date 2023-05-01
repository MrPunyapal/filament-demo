<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            //content will be html
            'content' => $this->faker->randomHtml,
            'is_visible' => $this->faker->boolean,
            //tags will be array of random words
            'tags' => $this->faker->words,
            // slug will be generated from title
            'slug' => $this->faker->slug,
            // published_at will bb recent date
            'published_at' => $this->faker->dateTimeBetween('-1 year'),
            // seo_title will have max 60 char
            'seo_title' => $this->faker->text(60),
            'seo_description' => $this->faker->text(160),
            'image' => $this->storeImage(),
        ];
    }

    /**
     * Store image in public disk
     *
     * @param string $url
     * @return string
     */
    private function storeImage(): string
    {
        $url = 'https://loremflickr.com/320/240/dog';
        $image = file_get_contents($url);
        $name = str()->uuid().'.jpg';
        $path = storage_path('app/public/' . $name);
        file_put_contents($path, $image);
        return $name;
    }
}
