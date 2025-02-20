<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLog extends Model
{
    // use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_log';

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
    protected $fillable = ['employee_id', 'type', 'amount', 'correction'];

    /**
     * Handling relation tables.
     *
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

    public function setCorrectionAttribute($value)
    {
        $this->attributes['correction'] = preg_replace("/[^\p{L}\p{N}\s]/u", "", $value);
    }

}
