<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate(array_merge_recursive(
            User::validationRules(),
            [
                "username" => "required",
                "email" => "required",
                "password" => "required",
            ]
        ));

        $user = new User(
            $request->only(['username', 'email', 'password'])
        );
        $user->password = Hash::make($user->password);
        $user->save();
        Auth::login($user, $request->input('remember'));

        $profile = new Profile([
            'name' => $user->username,
        ]);
        $user->profile()->save($profile);
        $user->data()->create();

        $user = Auth::user();
        return response()->json(['user' => $user], 200);
    }
}
