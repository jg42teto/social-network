<?php

namespace Database\Seeders;

use App\Models\MetaPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // parameters
        $knownUsers = [
            [
                "username" => 'cade',
                "admin" => '0',
            ],
            [
                "username" => 'jade',
                "admin" => '1',
            ],
        ];
        $users_no = 7;
        $min_following = 2;
        $max_following = 5;
        $min_posts_per_user = 100;
        $max_posts_per_user = 500;
        $posts_levels_ratio = [2, 10, 10, 5];
        $posts_levels_likes_avg = [3, 2, 1, 0.5]; //each must be <= users_no / 2
        $posts_levels_no = count($posts_levels_ratio);
        array_pad($posts_levels_likes_avg, $posts_levels_no, 0);

        // create users
        $users = User::factory()
            ->hasProfile()
            ->hasData()
            ->count($users_no - count($knownUsers))
            ->create();
        foreach ($knownUsers as $user) {
            $users->add(
                User::factory()
                    ->username($user["username"])
                    ->state($user)
                    ->hasProfile()
                    ->hasData()
                    ->create()
            );
        }

        // create users relationships
        foreach ($users as $user) {
            $following = $users->filter(
                function ($followed) use (&$user) {
                    return $followed->id != $user->id;
                }
            )->random(rand($min_following, $max_following));
            $user->following()->attach($following);
        }
        foreach ($users as $user) {
            $user->profile->following_number = $user->following()->count();
            $user->profile->followers_number = $user->followedBy()->count();
            $user->profile->save();
        }

        // create posts
        $posts_per_user = [];
        for ($i = 0; $i < $users_no; $i++) {
            $posts_per_user[] = rand($min_posts_per_user, $max_posts_per_user);
        }
        $posts_total = array_sum($posts_per_user);
        $posts = Post::factory()
            ->count($posts_total)
            ->create();

        // shuffling posts
        $posts = $posts->shuffle();

        // add authors to posts
        $first = 0;
        foreach ($users as $i => $user) {
            $user->posts()->saveMany(
                $posts->slice($first, $posts_per_user[$i])
            );
            $first += $posts_per_user[$i];
        }

        // init posts values
        foreach ($posts as $post) {
            $post->comments_number = 0;
            $post->likes_number = 0;
            $post->parent_post_id = null;
        }

        // sorting posts by id
        $posts = $posts->sortBy('id')->values();

        // metaposts
        MetaPost::insert(
            $posts->map(function ($post) {
                return [
                    'post_id' => $post->id,
                    'user_id' => $post->user_id,
                    'repost' => false,
                ];
            })->all()
        );

        // leveling (0 level posts are withouth parent, posts at the other levels have a parent at the previous level)
        $posts_levels = [];
        $ratio_numerator = array_sum($posts_levels_ratio);
        $first = 0;
        foreach ($posts_levels_ratio as $ratio_denominator) {
            $level_len = intval($posts_total * $ratio_denominator / $ratio_numerator);
            $posts_level = $posts->slice($first, $level_len);
            $first += $level_len;
            $posts_levels[] = $posts_level;
        }

        // following modification to posts are persisted at the end

        // hierarchy
        for ($i = count($posts_levels) - 1; $i > 0; $i--) {
            foreach ($posts_levels[$i] as $child) {
                $parent = $posts_levels[$i - 1]->random();
                $child->parent_post_id = $parent->id;
                $parent->comments_number += 1;
            }
        }

        // likes
        for ($i = 0; $i < count($posts_levels); $i++) {
            $max_likes_per_user = intval($posts_levels[$i]->count() * $posts_levels_likes_avg[$i] * 2 / $users_no);
            foreach ($users as $user) {
                $liked_posts = $posts_levels[$i]->random(rand(0, $max_likes_per_user));
                $user->likedPosts()->attach($liked_posts);
                foreach ($liked_posts as $post) {
                    $post->likes_number += 1;
                }
            }
        }


        // persisting posts changes
        $posts->transform(function ($post) {
            return [
                'id' => $post->id,
                'parent_post_id' => $post->parent_post_id,
                'comments_number' => $post->comments_number,
                'likes_number' => $post->likes_number,
            ];
        });

        Post::upsert(
            $posts->all(),
            ['id'],
            [
                'parent_post_id',
                'comments_number',
                'likes_number',
            ]
        );
    }
}
