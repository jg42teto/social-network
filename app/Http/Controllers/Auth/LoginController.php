<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'max:255'],
            'password' => ['required', 'max:255'],
        ]);
        if (Auth::attempt($request->only('username', 'password'), $request->remember)) {
            $user = Auth::user();
            if ($user->blocked) {
                Auth::logout();
                return response()->json(['errors' => [], 'message' => 'This account is blocked.'], 403);
            }
            return response()->json(['user' => $user], 200);
        } else {
            return response()->json(['errors' => [], 'message' => 'No such user credentials.'], 401);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['infos' => ["Successful logout."]], 200);
    }
}
