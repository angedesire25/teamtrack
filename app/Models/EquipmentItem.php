<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentItem extends Model
{
    use TenantScoped;

    protected $fillable = [
        'tenant_id', 'supplier_id', 'name', 'category', 'condition',
        'quantity_total', 'quantity_available', 'low_stock_threshold',
        'unit_price', 'reference', 'notes',
    ];

    protected $casts = [
        'quantity_total'      => 'integer',
        'quantity_available'  => 'integer',
        'low_stock_threshold' => 'integer',
        'unit_price'          => 'decimal:2',
    ];

    public function supplier(): BelongsTo   { return $this->belongsTo(Supplier::class); }
    public function movements(): HasMany    { return $this->hasMany(EquipmentMovement::class); }

    public function conditionLabel(): string
    {
        return match($this->condition) {
            'new'         => 'Neuf',
            'good'        => 'Bon état',
            'repair'      => 'À réparer',
            'out_of_service' => 'Hors service',
            default       => ucfirst($this->condition),
        };
    }

    public function conditionColor(): string
    {
        return match($this->condition) {
            'new'         => 'text-blue-600 bg-blue-50',
            'good'        => 'text-emerald-600 bg-emerald-50',
            'repair'      => 'text-amber-600 bg-amber-50',
            'out_of_service' => 'text-red-600 bg-red-50',
            default       => 'text-gray-600 bg-gray-50',
        };
    }

    public function isLowStock(): bool
    {
        return $this->quantity_available <= $this->low_stock_threshold;
    }

    public static function categories(): array
    {
        return ['Ballon','Cône','Chasuble','Filet','Goal','Mannequin','Haie','Médical','Électronique','Autre'];
    }
}
