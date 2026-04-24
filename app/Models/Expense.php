<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use TenantScoped;

    protected $fillable = ['tenant_id', 'category_id', 'amount', 'date', 'description', 'reference', 'paid_by', 'notes'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function category(): BelongsTo { return $this->belongsTo(ExpenseCategory::class, 'category_id'); }
}
