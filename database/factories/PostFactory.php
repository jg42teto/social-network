<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "content" => $this->faker->realText(200),
        ];
    }

    public function fakeStats()
    {
        return $this->state(function (array $attributes) {
            return [
                "comments_number" => rand(0, 20),
                "shares_number" => rand(0, 40),
                "likes_number" => rand(0, 200),
            ];
        });
    }
}
