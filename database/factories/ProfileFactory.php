<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bio' => $this->faker->text(200),
            'name' => $this->faker->name(),
        ];
    }

    public function fakeStats()
    {
        return $this->state(function (array $attributes) {
            return [
                'following_number' => rand(0, 20),
                'followers_number' => rand(0, 20),
            ];
        });
    }
}
