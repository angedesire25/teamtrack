<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jersey extends Model
{
    use TenantScoped;

    protected $fillable = [
        'tenant_id', 'supplier_id', 'name', 'type', 'season', 'color',
        'size', 'quantity_total', 'quantity_available', 'low_stock_threshold',
        'unit_price', 'notes',
    ];

    protected $casts = [
        'quantity_total'     => 'integer',
        'quantity_available' => 'integer',
        'low_stock_threshold'=> 'integer',
        'unit_price'         => 'decimal:2',
    ];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }

    public function assignments(): HasMany { return $this->hasMany(JerseyAssignment::class); }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(JerseyAssignment::class)->whereNull('returned_at');
    }

    public function typeLabel(): string
    {
        return match($this->type) {
            'home'     => 'Domicile',
            'away'     => 'Extérieur',
            'training' => 'Entraînement',
            'keeper'   => 'Gardien',
            default    => 'Autre',
        };
    }

    public function isLowStock(): bool
    {
        return $this->quantity_available <= $this->low_stock_threshold;
    }

    public static function sizes(): array
    {
        return ['XS','S','M','L','XL','XXL','XXXL','6 ans','8 ans','10 ans','12 ans','14 ans','16 ans'];
    }
}
