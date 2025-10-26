<?php

namespace App\Services\Balance\Dto;

use App\Helpers\Dto\Dto;
use App\Models\User;

class BalanceGetDto extends Dto
{
    public User $user;
}
