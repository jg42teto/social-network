<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    const LEN_INVALID_HASHTAG_REGEX = "/(?<![" .
        Hashtag::REGEX_SYMS .
        "&])#(?=[" .
        Hashtag::REGEX_SYMS .
        "]{" .
        (Hashtag::MAXLEN + 1) .
        ",})(" . Hashtag::REGEX_RAW .
        ")(?![" .
        Hashtag::REGEX_SYMS .
        "#])/i";

    public static function validationRules()
    {
        return [
            'content' => [
                'required',
                'string',
                'max:255',
                'not_regex:' . Post::LEN_INVALID_HASHTAG_REGEX,
            ],
            'shared_post_id' => ['prohibited_unless:parent_post_id,null', 'nullable', 'numeric'],
            'parent_post_id' => ['nullable', 'numeric'],
        ];
    }

    protected $fillable = [
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function childPosts()
    {
        return $this->hasMany(Post::class, 'parent_post_id');
    }

    public function parentPost()
    {
        return $this->belongsTo(Post::class, 'parent_post_id');
    }

    public function sharedPost()
    {
        return $this->belongsTo(Post::class, 'shared_post_id');
    }

    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'likes')->as('like');
    }

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'keywords')->as('keyword');
    }

    public function mentions()
    {
        return $this->belongsToMany(User::class, 'mentions')->as('mention');
    }
}
