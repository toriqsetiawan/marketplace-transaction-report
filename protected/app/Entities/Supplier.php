<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'supplier';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 'keterangan'
    ];

    public function products()
    {
        return $this->hasMany('App\Entities\Products');
    }
}
