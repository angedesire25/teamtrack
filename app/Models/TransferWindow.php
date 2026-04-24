<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;

class TransferWindow extends Model
{
    use TenantScoped;

    protected $fillable = ['tenant_id', 'name', 'type', 'start_date', 'end_date', 'is_active'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_active'  => 'boolean',
        ];
    }

    public function isCurrent(): bool
    {
        return $this->is_active && today()->between($this->start_date, $this->end_date);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'summer'  => 'Été',
            'winter'  => 'Hiver',
            default   => 'Personnalisée',
        };
    }

    public static function currentWindow(int $tenantId): ?self
    {
        return static::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->first();
    }
}
