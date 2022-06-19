<?php

namespace App\Http\Controllers;

use App\Services\HashtagService;
use App\Services\PostService;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    protected $postService;
    protected $hashtagService;

    public function __construct(PostService $postService, HashtagService $hashtagService)
    {
        $this->postService = $postService;
        $this->hashtagService = $hashtagService;
    }

    public function posts(Request $request, $keyword)
    {
        $cpag = $this->postService->recentPostWithKeywordIdsQuery($keyword)
            ->cursorPaginate(config('settings.posts_per_page'));
        $metaPosts = $this->postService->recentMetaPostsIdIn($cpag->getCollection()->pluck('id'));
        return $cpag->through(function ($row) use ($metaPosts) {
            return $metaPosts[$row->id];
        });
    }

    public function search(Request $request)
    {
        $request->validate([
            'term' => ['required']
        ]);
        $term = $request->input('term');
        $keywords = $this->hashtagService->search($term, config('settings.search_results_limit'));
        return response()->json($keywords);
    }
}
