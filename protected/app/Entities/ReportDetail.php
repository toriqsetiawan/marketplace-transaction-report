<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportDetail extends Model
{

    // use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_detail';

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
    // protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['varian_id', 'report_id', 'quantity', 'price_history', 'date_at', 'sub_total'];

    /**
     * Handling relation tables.
     *
     */
    public function varian()
    {
        return $this->belongsTo(\App\Entities\Varian::class);
    }

    public function report()
    {
        return $this->belongsTo(\App\Entities\Report::class);
    }

}
