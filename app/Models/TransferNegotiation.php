<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferNegotiation extends Model
{
    protected $fillable = ['transfer_id', 'date', 'note', 'amount_proposed', 'status_after'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function transfer(): BelongsTo { return $this->belongsTo(Transfer::class); }
}
