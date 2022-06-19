<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Post $post)
    {
        return
            $user->id == $post->user_id ||
            $user->admin == 2 ||
            ( #
                $user->admin == 1 &&
                $post->user()->get('admin') == 0 #
            );
    }
}
