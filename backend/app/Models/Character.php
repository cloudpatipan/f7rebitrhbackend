<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Role;

class Character extends Model
{
    use HasFactory;
    protected $table = 'characters';
    protected $fillable = [
        'avatar',
        'image',
        'name',
        'slug',
        'voice_actor',
        'description',
        'background',
        'role_id',
    ];
    
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
