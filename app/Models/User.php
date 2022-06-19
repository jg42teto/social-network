<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasApiTokens;

    const USERNAME_REGEX_RAW = "[a-zA-Z0-9_]*";
    const USERNAME_REGEX_SYMS = "a-zA-Z0-9_";
    const USERNAME_MAXLEN = 50;

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function validationRules()
    {
        return [
            'username' => ['max:20', 'regex:/^' . User::USERNAME_REGEX_RAW . '$/', 'unique:users', Rule::notIn([
                'login', 'register', '404', 'api', 'sanctum',
                'forgot-password', 'reset-password', 'storage', 'admin',
                'notifications'
            ])],
            'email' => ['max:255', 'email', 'unique:users'],
            'password' => ['max:127', 'min:3', 'confirmed'],
        ];
    }

    public function isOwner($thing)
    {
        return $this === $thing->user;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function data()
    {
        return $this->hasOne(UserData::class);
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'likes')->as('like');
    }

    public function followedBy()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id');
    }
}
