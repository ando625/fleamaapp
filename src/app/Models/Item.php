<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Order;

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

    /**
     * 出品者（User）とのリレーション
     * Item->user で出品者情報にアクセスできる
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * カテゴリとの多対多リレーション
     * $item->categories()->attach([1,2]); などで紐付け
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'items_categories');
    }
    
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    /**
     * お気に入りしたユーザーを取得
     */
    public function favoritedUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
        // 'favorites' は中間テーブル名です。必要に応じて修正
    }


    // Item.php
    public function isFavoritedBy($user)
    {
        // 自分のいいねは user_id と一致するかで判定
        return $this->user_id === $user->id;
    }

    // Item.php
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }


    public function comments() 
    {
        return $this->hasMany(Comment::class);
    }

    // いいね数を取得するアクセサ
    public function getFavoriteCountAttribute()
    {
        return $this->favorites()->count();
    }

    // コメント数を取得するアクセサ
    public function getCommentCountAttribute()
    {
        return $this->comments()->count();
    }
}
