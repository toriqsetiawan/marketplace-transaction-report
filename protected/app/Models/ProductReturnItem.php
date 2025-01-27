<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturnItem extends Model
{
    use HasFactory;

    protected $fillable = ['product_return_id', 'transaction_item_id', 'quantity', 'refund_amount'];

    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class);
    }

    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }
}
