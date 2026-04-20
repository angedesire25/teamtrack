<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationCampaign extends Model
{
    use TenantScoped;

    protected $fillable = [
        'tenant_id', 'title', 'description', 'cover_image_url',
        'goal_amount', 'collected_amount', 'suggested_amounts',
        'start_date', 'end_date', 'is_active', 'allow_recurring', 'allow_anonymous',
    ];

    protected $casts = [
        'goal_amount'       => 'decimal:2',
        'collected_amount'  => 'decimal:2',
        'suggested_amounts' => 'array',
        'start_date'        => 'date',
        'end_date'          => 'date',
        'is_active'         => 'boolean',
        'allow_recurring'   => 'boolean',
        'allow_anonymous'   => 'boolean',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'campaign_id');
    }

    public function completedDonations(): HasMany
    {
        return $this->hasMany(Donation::class, 'campaign_id')->where('status', 'completed');
    }

    public function progressPercent(): float
    {
        if (!$this->goal_amount || $this->goal_amount == 0) {
            return 0;
        }
        return min(100, round(($this->collected_amount / $this->goal_amount) * 100, 1));
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function defaultSuggestedAmounts(): array
    {
        return $this->suggested_amounts ?? [5000, 10000, 25000, 50000];
    }
}
