<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_transaction_id',
        'variant_id',
        'quantity',
        'price',
    ];

    public function returnTransaction()
    {
        return $this->belongsTo(ReturnTransaction::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
