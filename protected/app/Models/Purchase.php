<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_code',
        'purchase_date',
        'total_cost',
        'status',
        'supplier_id',
        'user_id',
        'note',
    ];

    public function setTotalCostAttribute($value)
    {
        $this->attributes['total_cost'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
