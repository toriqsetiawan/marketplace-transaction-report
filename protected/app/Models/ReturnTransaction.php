<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'return_date',
        'total_price',
        'user_id',
        'note',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(ReturnTransactionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
