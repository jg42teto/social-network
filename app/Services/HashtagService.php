<?php

namespace App\Services;

use App\Models\Hashtag;
use Illuminate\Support\Facades\DB;

class HashtagService
{
    public function search($term, $number)
    {
        return Hashtag::query()
            ->where('keyword', 'LIKE', '%' . $term . '%')
            ->select(['keyword'])
            ->orderByDesc(DB::raw("keyword = '$term'"))
            ->limit($number)
            ->get();
    }
}
