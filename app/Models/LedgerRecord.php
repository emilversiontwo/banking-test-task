<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $balance_id
 * @property string $amount
 * @property string $operation
 * @property string $comment
 * @property int $balance_after
 * @property int $to_balance_id
 * @property string $after
 */
class LedgerRecord extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['balance_id', 'amount', 'operation', 'balance_after'];

    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class, 'balance_id', 'id');
    }

    public function toBalance(): BelongsTo
    {
        return $this->belongsTo(Balance::class, 'to_balance_id', 'id');
    }

    public function getAmountAttribute(): string
    {
        return bcdiv((string)$this->attributes['amount'], '100', 2);
    }

    public function setAmountAttribute($value): void
    {
        $this->attributes['amount'] = (int) bcmul((string)$value, '100', 0);
    }

    public function getAfterAttribute(): string
    {
        return bcdiv((string)$this->attributes['balance_after'], '100', 2);
    }

    public function setAfterAttribute($value): void
    {
        $this->attributes['balance_after'] = (int) bcmul((string)$value, '100', 0);
    }
}
