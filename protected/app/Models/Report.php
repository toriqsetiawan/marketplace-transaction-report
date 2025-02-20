<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report';

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
        return $this->hasMany(ReportDetail::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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
