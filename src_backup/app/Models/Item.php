<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'description',
        'price',
        'condition_id',
        'status',
        'item_path',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'items_categories');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function favoritedUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }


    public function isFavoritedBy($user)
    {
        return $this->user_id === $user->id;
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }


    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getFavoriteCountAttribute()
    {
        return $this->favorites()->count();
    }

    public function getCommentCountAttribute()
    {
        return $this->comments()->count();
    }
}
