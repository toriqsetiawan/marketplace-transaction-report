<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_code',
        'purchase_date',
        'total_price',
        'status',
        'user_id',
        'note',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function setTotalPriceAttribute($value)
    {
        $this->attributes['total_price'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
