<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_code', 'note', 'status', 'user_id', 'type', 'total_price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setTotalPriceAttribute($value)
    {
        $this->attributes['total_price'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
