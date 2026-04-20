<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use TenantScoped;

    protected $fillable = ['tenant_id', 'name', 'min_age', 'max_age', 'gender', 'sort_order'];

    protected function casts(): array
    {
        return ['min_age' => 'integer', 'max_age' => 'integer', 'sort_order' => 'integer'];
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function teams(): HasMany    { return $this->hasMany(Team::class); }
    public function players(): HasMany  { return $this->hasMany(Player::class); }
}
