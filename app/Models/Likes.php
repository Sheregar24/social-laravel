<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    protected $table = 'Likes';
    protected $fillable = ['user_id', 'post_id'];
    use HasFactory;
    public function users()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function posts()
    {
        return $this->hasOne('App\Models\Post', 'id', 'post_id');
    }
}
