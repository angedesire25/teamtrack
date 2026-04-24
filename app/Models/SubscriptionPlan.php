<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use TenantScoped;

    protected $fillable = ['tenant_id', 'name', 'season', 'amount', 'frequency', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function playerSubscriptions(): HasMany { return $this->hasMany(PlayerSubscription::class, 'plan_id'); }

    public function frequencyLabel(): string
    {
        return match ($this->frequency) {
            'one_time'  => 'Unique',
            'monthly'   => 'Mensuel',
            'quarterly' => 'Trimestriel',
            'annual'    => 'Annuel',
            default     => $this->frequency,
        };
    }

    public static function currentSeason(): string
    {
        $year = now()->month >= 7 ? now()->year : now()->year - 1;
        return $year . '-' . ($year + 1);
    }
}
