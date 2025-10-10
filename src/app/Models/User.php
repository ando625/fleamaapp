<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Comment;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // User が出品した商品
    public function items()
    {
        return $this->hasMany(Item::class);  //hasMany は「1人のユーザーが複数の商品を持つ
    }

    

    // User がいいねした商品（中間テーブル favorites を使う）
    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites');
    }

    // app/Models/User.php
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function Orders()
    {
        return $this->hasMany(Order::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
