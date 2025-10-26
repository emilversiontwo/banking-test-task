<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $id
 * @property int $user_id
 * @property int $balance
 * @property string $amount
 */
class Balance extends Model
{
    public function ledger_records(): HasMany
    {
        return $this->hasMany(LedgerRecord::class, 'balance_id', 'id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getAmountAttribute(): string
    {
        return bcdiv((string)$this->attributes['balance'], '100', 2);
    }

    public function setAmountAttribute($value): void
    {
        $this->attributes['balance'] = (int) bcmul((string)$value, '100', 0);
    }
}
