<?php

namespace App\Services;

use App\Models\Mention;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function find($username, $loggedUser)
    {
        $following_query = DB::table('follows')
            ->select('followed_id')
            ->where('follower_id', $loggedUser->id);

        return User::with('profile')->where('username', $username)
            ->leftJoinSub($following_query, 'follows', 'users.id', '=', 'follows.followed_id')
            ->select(['users.*', DB::raw('not isnull(follows.followed_id) as following')])
            ->first();
    }

    public function update($user, $updates)
    {
        if ($updates->has('bio')) {
            $user->profile->bio = $updates['bio'] ?? '';
        }

        if ($updates->has('name')) {
            if ($updates['name'] === null || $updates['name'] === '') {
                $user->profile->name = $user->username;
            } else {
                $user->profile->name = $updates['name'];
            }
        }

        if ($updates->has('email')) {
            $user->email = $updates['email'];
        }

        if ($updates->has('password')) {
            $user->password = Hash::make($updates['password']);
        }

        if ($user->isDirty()) $user->save();
        if ($user->profile->isDirty()) $user->profile->save();

        return $user;
    }

    public function toggleFollow($sourceUser, $targetUserId)
    {
        $toggleResult = $sourceUser->following()->toggle($targetUserId);
        if (empty($toggleResult['attached'])) {
            $following = false;
            $followingChange = -1;
        } else {
            $following = true;
            $followingChange = +1;
        }

        Profile::query()->where("user_id", $sourceUser->id)->increment("following_number", $followingChange);
        Profile::query()->where("user_id", $targetUserId)->increment("followers_number", $followingChange);

        return $following;
    }

    public function search($term, $number)
    {
        return User::join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('username', 'LIKE', '%' . $term . '%')
            ->orWhere('profiles.name', 'LIKE', '%' . $term . '%')
            ->select(['username', 'profiles.name'])
            ->orderByDesc(DB::raw("username = '$term'"))
            ->orderByDesc('profiles.followers_number')
            ->limit($number)
            ->get();
    }

    public function userData($user)
    {
        $notificationsNumber = Mention::query()
            ->where('user_id', $user->id)
            ->where('post_id', '>', $user->data->last_seen_mentioning_post_id)
            ->whereHas('post')
            ->count();
        return [
            'notifications_number' => $notificationsNumber,
        ];
    }

    public function notificationsChecked($user, $lastSeenMentioningPostId)
    {
        $user->data()->update(['last_seen_mentioning_post_id' => $lastSeenMentioningPostId]);
    }
}
