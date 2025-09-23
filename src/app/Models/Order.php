<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Item;


class Order extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'item_id',
        'recipient_name',
        'recipient_postal',
        'recipient_address',
        'recipient_building',
        'payment_method',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }
}
