<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function viewUser(Request $request, $username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json("No such user with username '$username'!", 404);
        }

        return response()->json($user);
    }

    public function toggleBlock(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json("No such user found!", 404);
        }

        $user->blocked = $user->blocked ? false : true;
        $user->save();
        return response()->json(["id" => $user->id, "blocked" => $user->blocked]);
    }

    public function viewAdmins(Request $request)
    {
        $users = User::where("admin", ">", 0)
            ->orderByDesc("admin")
            ->orderBy("username")
            ->get();
        return response()->json($users);
    }

    public function toggleAdmin(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json("No such user found!", 404);
        }

        if ($user->blocked && $user->admin == 0) {
            return response()->json("Blocked user can't become admin!", 400);
        }

        $user->admin = $user->admin ? 0 : 1;
        $user->save();
        return response()->json(["id" => $user->id, "admin" => $user->admin]);
    }
}
