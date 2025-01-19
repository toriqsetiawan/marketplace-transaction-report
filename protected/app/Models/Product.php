<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

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
        'supplier_id', 'sku', 'nama', 'ukuran', 'harga_beli', 'harga_tambahan', 'harga_online', 'harga_offline', 'harga_mitra'
    ];

    /**
     * Handling relation tables.
     *
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = ucwords($value);
    }

    public function setHargaBeliAttribute($value)
    {
        $this->attributes['harga_beli'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function setHargaTambahanAttribute($value)
    {
        $this->attributes['harga_tambahan'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function setHargaOnlineAttribute($value)
    {
        $this->attributes['harga_online'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function setHargaOfflineAttribute($value)
    {
        $this->attributes['harga_offline'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function setHargaMitraAttribute($value)
    {
        $this->attributes['harga_mitra'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }
}
