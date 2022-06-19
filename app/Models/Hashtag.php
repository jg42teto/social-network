<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    const REGEX_RAW = "[0-9_]*[a-z][a-z0-9_]*";
    const REGEX_SYMS = "a-z0-9_";
    const MAXLEN = 100;

    protected $fillable = ['keyword'];
    public $timestamps = false;
}
