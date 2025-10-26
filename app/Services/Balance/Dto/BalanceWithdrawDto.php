<?php

namespace App\Services\Balance\Dto;

use App\Helpers\Dto\Dto;

class BalanceWithdrawDto extends Dto
{
    public int $user_id;

    public string $amount;

    public string $comment;
}
