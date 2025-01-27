<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'varian_id',
        'qty',
        'price',
    ];

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function varian()
    {
        return $this->belongsTo(ProductVariant::class, 'varian_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
