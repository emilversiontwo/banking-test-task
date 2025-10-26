<?php

namespace App\Enums\Balance;

enum BalanceOperationTypeEnum: string
{
    case Deposit = 'deposit';

    case Withdraw = 'withdraw';

    case TransferOUT = 'transfer_out';

    case TransferIN = 'transfer_in';
}
