<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Varian extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'varian';

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
    protected $fillable = ['nama', 'taxonomi_id', 'harga_satuan'];

    /**
     * Handling relation tables.
     *
     */
    public function taxonomi()
    {
        return $this->belongsTo(Taxonomi::class);
    }

    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = ucwords($value);
    }

    public function setHargaSatuanAttribute($value)
    {
        $this->attributes['harga_satuan'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }
}
