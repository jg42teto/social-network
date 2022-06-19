<?php

namespace App\Services;

use App\Models\Hashtag;
use App\Models\Keyword;
use App\Models\Like;
use App\Models\Mention;
use App\Models\MetaPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PostService
{
    const HASHTAG_REGEX = "/(?<![" . Hashtag::REGEX_SYMS . "&])#(" . Hashtag::REGEX_RAW . ")(?![" . Hashtag::REGEX_SYMS . "#])/i";
    const MENTION_REGEX = "/(?<![" . User::USERNAME_REGEX_SYMS . "@#&])@(" . User::USERNAME_REGEX_RAW . ")(?![" . User::USERNAME_REGEX_SYMS . "@])/"; // not insensitive

    public function create($userId, $content, $sharedId, $parentId)
    {
        # process hashtags 
        $keywords = $this->extractKeywords($content);
        $existing_kws = Hashtag::whereIn("keyword", $keywords)->get();
        $new_kws = array_diff($keywords, $existing_kws->pluck("keyword")->toArray());
        $new_kws = array_map(function ($kw) {
            return ["keyword" => $kw];
        }, $new_kws);

        # process mentions
        $mentions = $this->extractMentions($content);
        $mentioned_users = User::query()
            ->select(['id', 'username'])
            ->whereIn('username', $mentions)
            ->get();
        $mentionIds = $mentioned_users
            ->map(function ($user) {
                return $user->id;
            })->all();
        $mentioned_users_string = $mentioned_users
            ->map(function ($user) {
                return $user->username;
            })
            ->join(',');

        # post
        $post = new Post();
        $post->user()->associate($userId);
        $post->content = $content;
        $post->mentioned_users = $mentioned_users_string;
        $post->parentPost()->associate($parentId);
        Post::withTrashed()->whereId($parentId)->increment('comments_number');
        $post->sharedPost()->associate($sharedId);
        Post::withTrashed()->whereId($sharedId)->increment('shares_number');
        $post->save();

        # attach hashtags
        $post->hashtags()->saveMany($existing_kws);
        $post->hashtags()->createMany($new_kws);

        # attach mentions
        $post->mentions()->attach($mentionIds);

        # metapost
        $metaPost = MetaPost::ctor($post->id, $userId, false);
        $metaPost->save();
        $metaPost->load('post');
        return $metaPost;
    }

    protected function extractKeywords($text)
    {
        preg_match_all(PostService::HASHTAG_REGEX, $text, $matches);
        $keywords = $matches[1];
        $keywords = array_map(
            function ($kw) {
                return strtolower($kw);
            },
            $keywords
        );
        $keywords = array_unique($keywords);
        return $keywords;
    }

    protected function extractMentions($text)
    {
        preg_match_all(PostService::MENTION_REGEX, $text, $matches);
        $mentions = $matches[1];
        $mentions = array_unique($mentions);
        return $mentions;
    }

    public function delete($postId)
    {
        $post = Post::find($postId);
        Post::withTrashed()->whereId($post->parent_post_id)->decrement('comments_number');
        Post::withTrashed()->whereId($post->shared_post_id)->decrement('shares_number');
        MetaPost::query()
            ->where('post_id', $postId)
            ->update(['deleted_at' => now()]);
        $post->delete();
    }

    public function toggleRepost($userId, $postId)
    {
        $metaPost = MetaPost::ctor($postId, $userId, true);
        $deleted = MetaPost::where($metaPost->toArray())->forceDelete();
        if ($deleted) {
            Post::withTrashed()->whereId($postId)->decrement('shares_number');
            return null;
        }
        // Could be chacked if the post is deleted in the meantime
        // for lowering chance of reposting a deleted post.
        // Client-side check for empty metaposts must be done for sure.
        $metaPost->save();
        Post::withTrashed()->whereId($postId)->increment('shares_number');
        return $metaPost;
    }

    public function toggleLike($userId, $postId)
    {
        if (!$post = Post::find($postId)) return null;
        $result = $post->likedBy()->toggle($userId);
        $liked = !empty($result['attached']);
        $post->likes_number += $liked ? 1 : -1;
        $post->save();
        return $liked;
    }

    public function likedOf($userId, $postIds)
    {
        return Like::query()
            ->select(['post_id'])
            ->where('user_id', $userId)
            ->whereIn('post_id', $postIds)
            ->get()
            ->pluck('post_id');
    }

    public function sharedOf($userId, $postIds)
    {
        return MetaPost::query()
            ->select(['post_id'])
            ->where('user_id', $userId)
            ->where('repost', true)
            ->whereIn('post_id', $postIds)
            ->get()
            ->pluck('post_id');
    }

    public function recentFollowedPostsQuery($userId)
    {
        $following_query = DB::table('follows')
            ->select('followed_id')
            ->where('follower_id', $userId);

        return $this
            ->recentMetaPostsQuery()
            ->whereHas('post', function ($query) {
                $query->whereNull('parent_post_id');
            })
            ->joinSub($following_query, 'follows', 'user_id', '=', 'follows.followed_id');
    }

    public function recentUserPostsAndRepliesQuery($userId)
    {
        return $this
            ->recentMetaPostsQuery()
            ->where('user_id', $userId);
    }

    public function recentUserPostsQuery($userId)
    {
        return $this
            ->recentMetaPostsQuery()
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->whereHas('post', function ($query) {
                    $query->whereNull('parent_post_id');
                });
                $query->orWhere('repost', true);
            });
    }

    public function recentRepliesQuery($postId)
    {
        return $this
            ->recentMetaPostsQuery()
            ->whereHas('post', function ($query) use ($postId) {
                $query->where('parent_post_id', $postId);
            });
    }

    public function recentUserLikedPostIdsQuery($userId)
    {
        return Like::query()
            ->select(DB::raw('post_id as id'))
            ->orderByDesc('id')
            ->where('user_id', $userId);
    }

    public function recentMentioningPostIdsQuery($userId)
    {
        return Mention::query()
            ->select(DB::raw('post_id as id'))
            ->orderByDesc('id')
            ->where('user_id', $userId)
            ->whereHas('post');
    }

    public function recentPostWithKeywordIdsQuery($keyword)
    {
        return Keyword::query()
            ->select(DB::raw('post_id as id'))
            ->orderByDesc('id')
            ->whereHas('hashtag', function ($query) use ($keyword) {
                $query->where('keyword', strtolower($keyword));
            });
    }

    public function ancestryPostIds($postId)
    {
        // query->with(...) doesn't work with recursion 
        $recursion_block_query = Post::query()
            ->select(['id', 'parent_post_id'])
            ->where('id', $postId)
            ->unionAll(
                Post::withTrashed()
                    ->select(['posts.id', 'posts.parent_post_id'])
                    ->join('agg', 'agg.parent_post_id', '=', 'posts.id')
            );

        $post_ids = DB::table('agg')
            ->withRecursiveExpression('agg', $recursion_block_query)
            ->select('id')
            ->get()->map(function ($row) {
                return $row->id;
            });

        return $post_ids;
    }

    public function recentMetaPostsIdIn($postIds)
    {
        $metaPosts = $this->recentMetaPostsQuery()
            ->where('repost', false)
            ->whereIn('post_id', $postIds)
            ->get()
            ->keyBy('post_id');
        return collect($postIds)->flip()->transform(function ($_, $key) use ($metaPosts) {
            return $metaPosts[$key] ?? null;
        });
    }

    protected function recentMetaPostsQuery($query = null)
    {
        return ($query ?: MetaPost::query())
            ->with([
                'user',
                'post.user.profile',
                'post.sharedPost.user.profile',
            ])
            ->whereNull('deleted_at')
            ->orderByDesc('id');
    }
}
