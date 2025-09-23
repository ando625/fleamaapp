<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'description',
        'price',
        'condition',
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
}
