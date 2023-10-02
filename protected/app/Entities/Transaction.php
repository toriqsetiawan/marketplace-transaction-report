<?php

namespace App\Entities;

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
        'product_id', 'channel', 'name', 'marketplace', 'jumlah', 'ukuran', 'motif', 'harga_beli', 'harga_jual',
        'biaya_tambahan', 'biaya_lain_lain', 'pajak', 'total_paid', 'status', 'keterangan'
    ];

    public function mitra()
    {
        return $this->belongsTo('App\Entities\Mitra', 'name', 'id_mitra');
    }

    public function configFee()
    {
        return $this->belongsTo('App\Entities\ConfigFee', 'marketplace');
    }

    public function product()
    {
        return $this->belongsTo('App\Entities\Product');
    }

    public function getStatusAttribute($value)
    {
        if ($value == 1) {
            return 'Pending';
        } elseif ($value == 2) {
            return 'Lunas';
        } elseif ($value == 3) {
            return 'Retur';
        } else {
            return '-';
        }
    }
}
