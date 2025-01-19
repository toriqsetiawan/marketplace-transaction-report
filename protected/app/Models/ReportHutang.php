<?php

namespace App\Models;

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
        return $this->belongsTo(Employee::class);
    }

    public function hutang()
    {
        return $this->belongsTo(Hutang::class);
    }
}
