<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'

    ];


    // 多対多　カテゴリに属するアイテム
    public function items()
    {
        return $this->belongsToMany(Item::class, 'items_categories');
    }
}
