<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id', 'return_date', 'status', 'total_refund'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function returnItems()
    {
        return $this->hasMany(ProductReturnItem::class);
    }
}
