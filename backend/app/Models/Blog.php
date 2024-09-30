<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Comment;

class Blog extends Model
{
    use HasFactory;
    protected $table = 'post';
    protected $fillable = [
        'user_id',
        'name',
        'image',
        'content',
        'view',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
