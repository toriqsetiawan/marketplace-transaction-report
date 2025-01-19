<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ReportHutang extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_hutang';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['employee_id', 'hutang_id', 'jumlah_uang'];

    /**
     * Handling relation tables.
     *
     */
    public function employee()
    {
        return $this->belongsTo(\App\Entities\Employee::class);
    }

    public function hutang()
    {
        return $this->belongsTo(\App\Entities\Hutang::class);
    }
}
