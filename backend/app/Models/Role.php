<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Character;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = [
        'slug',
        'name',
    ];

    public function character()
    {
        return $this->hasMany(Character::class, 'role_id');
    }
    
}
