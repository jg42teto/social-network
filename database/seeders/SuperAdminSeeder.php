<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userState = config("superadmin.user");
        $userState['password'] = Hash::make($userState['password']);
        User::factory()
            ->state(
                $userState
            )
            ->hasProfile(
                config("superadmin.profile")
            )
            ->hasData()
            ->create();
    }
}
