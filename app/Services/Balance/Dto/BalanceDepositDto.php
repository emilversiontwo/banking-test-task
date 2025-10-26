<?php

namespace App\Services\Balance\Dto;

use App\Helpers\Dto\Dto;
use App\Models\Balance;
use App\Models\User;

class BalanceDepositDto extends Dto
{
    public int $user_id;

    public string $amount;

    public string $comment;
}
