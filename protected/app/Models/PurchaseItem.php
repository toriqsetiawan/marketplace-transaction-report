<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_id',
        'variant_id',
        'quantity',
        'price',
    ];

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
