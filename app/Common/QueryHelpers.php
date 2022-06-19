<?php

namespace App\Common;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryHelpers
{
    public static function postsWithLikedQuery(Request $request)
    {
        return QueryHelpers::postsWithLiked(Post::query(), $request);
    }
    public static function postsWithLiked($query, Request $request)
    {
        $liked_query = DB::table('likes')
            ->select(['post_id'])
            ->where('user_id', $request->user()->id);

        $postsWithLikedQuery =  $query
            ->select(DB::raw('posts.*, not isnull(likes.post_id) as liked'))
            ->leftJoinSub($liked_query, 'likes', 'posts.id', '=', 'likes.post_id');

        return $postsWithLikedQuery;
    }
}
