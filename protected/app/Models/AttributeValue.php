<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['attribute_id', 'value'];

    // Relationship to Attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    // Relationship to VariantAttributeValues
    public function variantAttributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class);
    }
}
