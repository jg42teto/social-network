<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio',
    ];

    public static function validationRules()
    {
        return [
            'bio' => ["max:255"],
            'name' => ["max:100"],
        ];
    }

    public function getAvatarAttribute($value)
    {
        // // if app host name doesn't start with two slashes 
        // if ($value == null) return null;
        // preg_match("/\/.+/", Storage::disk('avatars')->url($value), $matches);
        // return $matches[0];
        return $value ? Storage::disk('avatars')->url($value) : null;
    }

    public $timestamps = false;
}
