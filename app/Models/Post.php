<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'postcontent',
        'user_id'
    ];
    public function users()
    {
        return $this->hasOne('App\Models\User', "id", "user_id");
    }
}
