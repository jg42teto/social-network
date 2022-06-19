<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        do {
            $username = str_replace('.', '_', $this->faker->unique()->userName());
        } while (strlen($username) > 20);
        return $this->usernameBasedUserAttrs($username);
    }

    public function username($username)
    {
        return $this->state(function (array $attributes) use ($username) {
            return $this->usernameBasedUserAttrs($username);
        });
    }

    private function usernameBasedUserAttrs($username)
    {
        return [
            'username' => $username,
            'email' => $username . '@' . $this->faker->safeEmailDomain,
            'password' => Hash::make($username)
        ];
    }
}
