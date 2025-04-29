<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdwordsReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'checksum', 'date', 'description', 'total', 'note'
    ];
}
