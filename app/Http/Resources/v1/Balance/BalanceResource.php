<?php

namespace App\Http\Resources\v1\Balance;

use App\Models\Balance;
use App\Models\LedgerRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Balance
 */
class BalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'amount' => $this->amount,
        ];
    }
}
