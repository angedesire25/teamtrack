<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use TenantScoped;

    protected $fillable = ['tenant_id', 'name', 'color', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function expenses(): HasMany { return $this->hasMany(Expense::class, 'category_id'); }

    public static function defaults(): array
    {
        return [
            ['name' => 'Salaires & Primes',    'color' => '#7C3AED'],
            ['name' => 'Matériel & Équipement', 'color' => '#2563EB'],
            ['name' => 'Déplacements',           'color' => '#EA580C'],
            ['name' => 'Infrastructure',         'color' => '#16A34A'],
            ['name' => 'Communication',          'color' => '#0891B2'],
            ['name' => 'Autres',                 'color' => '#6B7280'],
        ];
    }
}
