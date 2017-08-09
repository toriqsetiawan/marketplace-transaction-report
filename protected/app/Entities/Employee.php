<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee';

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
    protected $fillable = ['nama', 'alamat', 'phone', 'golongan'];

    /**
     * Handling relation tables.
     *
     */
    public function report()
    {
        return $this->hasMany('App\Entities\Report');
    }

    public function bon()
    {
        return $this->hasOne('App\Entities\Report')->where('type', 'bon');
    }

    public function log()
    {
        return $this->hasOne('App\Entities\EmployeeLog');
    }

    public function lastTransaction()
    {
        return $this->hasOne('App\Entities\EmployeeLog');
    }

    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = ucwords($value);
    }

    public function setAlamatAttribute($value)
    {
        $this->attributes['alamat'] = ucwords($value);
    }

}
