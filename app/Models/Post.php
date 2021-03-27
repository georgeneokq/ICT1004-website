<?php
namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function images() {
        return $this->hasMany(PostImage::class);
    }

    public function likes() {
        return $this->hasMany(PostLike::class);
    }
}
