<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Services\UserService;

class UserController extends Controller
{
    protected $postService;
    protected $userService;

    public function __construct(PostService $postService, UserService $userService)
    {
        $this->postService = $postService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'username' => ['required']
        ]);
        $username = $request->input('username');
        $user = $this->userService->find($username, $request->user());
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $request->validate(array_merge(
            User::validationRules(),
            Profile::validationRules()
        ));

        $user = $request->user();
        $updates = collect($request->only(['bio', 'name', 'email', 'password']));
        $user = $this->userService->update($user, $updates);
        return response()->json($user);
    }

    public function likedPosts(Request $request, $id)
    {
        $cpag = $this->postService->recentUserLikedPostIdsQuery($id)
            ->cursorPaginate(config('settings.posts_per_page'));
        $metaPosts = $this->postService->recentMetaPostsIdIn($cpag->getCollection()->pluck('id'));
        return $cpag->through(function ($row) use ($metaPosts) {
            return $metaPosts[$row->id];
        });
    }

    public function allPosts(Request $request, $id)
    {
        return $this->postService->recentUserPostsAndRepliesQuery($id)
            ->cursorPaginate(config('settings.posts_per_page'));
    }

    public function wallPosts(Request $request, $id)
    {
        return $this->postService->recentUserPostsQuery($id)
            ->cursorPaginate(config('settings.posts_per_page'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'term' => ['required']
        ]);
        $term = $request->input('term');
        $users = $this->userService->search($term, config('settings.search_results_limit'));
        return response()->json($users);
    }

    public function toggleFollow(Request $request, $id)
    {
        $following = $this->userService->toggleFollow($request->user(), $id);
        return response()->json([
            "id" => $id,
            "following" => $following,
        ]);
    }

    public function userData(Request $request)
    {
        $data = $this->userService->userData($request->user());
        return response()->json($data);
    }

    public function notificationsChecked(Request $request)
    {
        $request->validate([
            'last_seen_mentioning_post_id' => ['required']
        ]);
        $post_id = $request->input('last_seen_mentioning_post_id');
        $user = $request->user();
        $this->userService->notificationsChecked($user, $post_id);
        return response()->noContent();
    }
}
