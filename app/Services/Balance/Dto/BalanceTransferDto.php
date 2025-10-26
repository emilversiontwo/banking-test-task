<?php

namespace App\Services\Balance\Dto;

use App\Helpers\Dto\Dto;

class BalanceTransferDto extends Dto
{
    public int $from_user_id;

    public int $to_user_id;

    public string $amount;

    public string $comment;
}
