<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PublicStorageController extends Controller
{
    public function store(Request $request)
    {
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => [
                    'max:24',
                    'image',
                    Rule::dimensions()->maxWidth(256)->maxHeight(256),
                ]
            ]);

            $profile = $request->user()->profile;

            $old_avatar_path = $profile->getAttributes()['avatar'];
            Storage::disk('avatars')->delete($old_avatar_path);

            $new_avatar_path = $request->file('avatar')
                ->store('', 'avatars');
            $profile->avatar = $new_avatar_path;
            $profile->save();
            $new_avatar_url = $profile->avatar;
            return response()->json(["avatar" => $new_avatar_url]);
        }
    }
}
