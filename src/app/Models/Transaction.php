<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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

    public function isBothReviewed(): bool
    {
        return $this->buyer_reviewed && $this->seller_reviewed;
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

}
