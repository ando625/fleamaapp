<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'sender_id',
        'body',
        'image_path',
        'is_read',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
