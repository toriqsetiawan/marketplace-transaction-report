<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hutang';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['employee_id', 'nama', 'harga', 'angsuran', 'status'];

    /**
     * Handling relation tables.
     *
     */
    public function employee()
    {
        return $this->belongsTo('App\Entities\Employee');
    }
}
