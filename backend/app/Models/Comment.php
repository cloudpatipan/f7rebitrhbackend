<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Blog;

class Comment extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'comment';
    protected $fillable = [
        'post_id',
        'user_id',
        'content',
    ];

    protected $with = ['user'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Blog::class, 'post_id');
    }
}
