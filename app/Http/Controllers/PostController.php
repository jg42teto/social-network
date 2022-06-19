<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function store(Request $request)
    {
        $request->validate(Post::validationRules());
        $metaPost = $this->postService->create(
            $request->user()->id,
            $request->content,
            $request->shared_post_id,
            $request->parent_post_id
        );
        return response()->json($metaPost);
    }

    public function destroy(Request $request, $id)
    {
        $this->postService->delete($id);
        return response()->noContent();
    }

    public function toggleLike(Request $request, $id)
    {
        $liked = $this->postService->toggleLike($request->user()->id, $id);
        return response()->json([
            "id" => $id,
            "liked" => $liked,
        ]);
    }

    public function toggleRepost(Request $request, $id)
    {

        $metaPost = $this->postService->toggleRepost($request->user()->id, $id);
        return response()->json([
            "id" => $id,
            "shared" => !!$metaPost,
        ]);
    }

    public function userSpecificInfo(Request $request)
    {
        $postIds = $request->input('ids');
        $postIds = collect(explode(",", $postIds))->transform(function ($id) {
            return intval($id);
        });
        $userId = $request->user()->id;

        $posts_info = $postIds
            ->flip()
            ->transform(function ($_, $key) {
                return collect([
                    'id' => $key,
                    'liked' => false,
                    'shared' => false,
                ]);
            });

        $this->postService
            ->likedOf($userId, $postIds)
            ->each(function ($id) use ($posts_info) {
                $posts_info[$id]['liked'] = true;
            });

        $this->postService
            ->sharedOf($userId, $postIds)
            ->each(function ($id) use ($posts_info) {
                $posts_info[$id]['shared'] = true;
            });

        return $posts_info->values();
    }

    public function mentions(Request $request)
    {
        $cpag = $this->postService->recentMentioningPostIdsQuery($request->user()->id)
            ->cursorPaginate(config('settings.posts_per_page'));
        $metaPosts = $this->postService->recentMetaPostsIdIn($cpag->getCollection()->pluck('id'));
        return $cpag->through(function ($row) use ($metaPosts) {
            return $metaPosts[$row->id];
        });
    }

    public function home(Request $request)
    {
        return $this->postService->recentFollowedPostsQuery($request->user()->id)
            ->cursorPaginate(config('settings.posts_per_page'));
    }

    public function ancestry(Request $request, $id)
    {
        $postIds = $this->postService->ancestryPostIds($id);
        $posts = $this->postService->recentMetaPostsIdIn($postIds)->values();
        return $posts;
    }

    public function replies(Request $request, $id)
    {
        return $this->postService->recentRepliesQuery($id)
            ->cursorPaginate(config('settings.posts_per_page'));
    }
}
