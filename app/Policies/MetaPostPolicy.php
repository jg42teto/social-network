<?php

namespace App\Policies;

use App\Models\MetaPost;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MetaPostPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, MetaPost $metaPost)
    {
        return
            $user->id == $metaPost->user_id ||
            $user->admin == 2 ||
            ( #
                $user->admin == 1 &&
                $metaPost->user()->get('admin') == 0 #
            );
    }
}
