<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $table = 'favorites';
    protected $fillable = [
        'user_id',
        'character_id',
    ];

    protected $with = ['character', 'user'];
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function character() {
        return $this->belongsTo(Character::class, 'character_id');
    }
}
