<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{

    // use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report';

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
    protected $fillable = ['id', 'employee_id', 'name', 'type', 'total', 'kodi', 'count', 'date_at'];

    /**
     * Handling relation tables.
     *
     */
    public function detail()
    {
        return $this->hasMany('App\Entities\ReportDetail');
    }

    public function employee()
    {
        return $this->belongsTo('App\Entities\Employee');
    }

    public function scopeBon($query)
    {
        return $query->whereType('bon');
    }

    public function scopeSetor($query)
    {
        return $query->whereType('setor');
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

}
