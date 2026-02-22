<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBomVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_bom_id',
        'version',
        'effective_from',
        'effective_to',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function bom()
    {
        return $this->hasOne(ProductionBom::class, 'id', 'production_bom_id');
    }

    public function lines()
    {
        return $this->hasMany(ProductionBomLine::class, 'production_bom_version_id');
    }
}
