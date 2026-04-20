<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use TenantScoped;

    protected $fillable = ['tenant_id', 'category_id', 'coach_id', 'name'];

    public function tenant(): BelongsTo   { return $this->belongsTo(Tenant::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function coach(): BelongsTo    { return $this->belongsTo(User::class, 'coach_id'); }
    public function players(): HasMany    { return $this->hasMany(Player::class); }
}
