<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(["username" => ["required", "max:255", "exists:users"]]);

        $status = Password::sendResetLink(
            $request->only("username")
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(["message" => __($status)])
            : response()->json(["errors" => [], "message" => __($status)], 422);
    }

    public function reset(Request $request)
    {
        $request->validate(array_merge_recursive(
            Arr::only(User::validationRules(), ["password"]),
            [
                "username" => ["required", "max:255", "exists:users"],
                "password" => ["required"],
                "token" => ["required", "max:255"],
            ]
        ));

        $status = Password::reset(
            $request->only("username", "password", "password_confirmation", "token"),
            function ($user, $password) {
                $user->forceFill([
                    "password" => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(["message" => __($status)])
            : response()->json(["errors" => [], "message" => __($status)], 422);
    }
}
