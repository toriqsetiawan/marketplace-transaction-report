<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id', 'varian_id', 'quantity', 'price'
    ];

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function varian()
    {
        return $this->belongsTo(ProductVariant::class, 'varian_id');
    }
}
