<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//誰と誰が、どの商品を取引してるか 取引部屋
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'seller_id',
        'status',
        'completed_at',
        'buyer_reviewed',
        'seller_reviewed',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'buyer_reviewed' => 'boolean',
        'seller_reviewed' => 'boolean',
    ];

    // 両方評価済みか判定
    public function isBothReviewed(): bool
    {
        return $this->buyer_reviewed && $this->seller_reviewed;
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    //購入者
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // 出品者
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    //取引チャット
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    //取引後の評価
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


}
