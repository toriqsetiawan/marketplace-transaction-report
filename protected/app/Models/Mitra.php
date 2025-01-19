<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mitra';

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
        'id_mitra', 'nama'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'name', 'id_mitra');
    }
}
