<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // image need to be stored in public folder
            'image' => $this->storeImage(),
            // alt can be null or filled
            'alt' => $this->faker->optional()->text
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
