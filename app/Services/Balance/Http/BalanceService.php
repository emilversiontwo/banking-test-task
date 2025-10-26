<?php

namespace App\Services\Balance\Http;

use App\Enums\Balance\BalanceOperationTypeEnum;
use App\Exceptions\BalanceLogicException;
use App\Models\Balance;
use App\Models\LedgerRecord;
use App\Models\User;
use App\Services\Balance\Dto\BalanceDepositDto;
use App\Services\Balance\Dto\BalanceGetDto;
use App\Services\Balance\Dto\BalanceTransferDto;
use App\Services\Balance\Dto\BalanceWithdrawDto;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BalanceService
{
    /**
     * @throws Throwable
     * @throws BalanceLogicException
     */
    public function deposit(BalanceDepositDto $dto): Balance
    {
        DB::beginTransaction();

        $user = User::findOrFail($dto->user_id);

        $balance = $this->getOrCreateBalance($user);

        $ledgerRecord = new LedgerRecord();
        $ledgerRecord->balance_id = $balance->id;
        $ledgerRecord->comment = $dto->comment;
        $ledgerRecord->amount = $dto->amount;
        $ledgerRecord->operation = BalanceOperationTypeEnum::Deposit->value;
        $ledgerRecord->after = bcadd($balance->amount, $dto->amount, 2);

        $balance->amount = bcadd($balance->amount, $dto->amount, 2);

        if ($balance->amount !== $ledgerRecord->after) {
            throw new BalanceLogicException('Error deposit money to the balance', Response::HTTP_CONFLICT);
        }

        $ledgerRecord->save();
        $balance->save();

        DB::commit();

        $balance->load(['ledger_records' => function ($q) use ($ledgerRecord) {
            $q->where('id', $ledgerRecord->id);
        }]);

        return $balance;
    }

    /**
     * @throws Throwable
     * @throws BalanceLogicException
     */
    public function withdraw(BalanceWithdrawDto $dto): Balance
    {
        DB::beginTransaction();

        $user = User::findOrFail($dto->user_id);

        $balance = $this->getOrCreateBalance($user);

        if (bccomp($dto->amount, $balance->amount, 2) === 1) {
            throw new BalanceLogicException('Your account has insufficient funds to complete this transaction', Response::HTTP_CONFLICT);
        }

        $ledgerRecord = new LedgerRecord();
        $ledgerRecord->balance_id = $balance->id;
        $ledgerRecord->comment = $dto->comment;
        $ledgerRecord->amount = $dto->amount;
        $ledgerRecord->operation = BalanceOperationTypeEnum::Withdraw->value;
        $ledgerRecord->after = bcsub($balance->amount, $dto->amount, 2);

        $balance->amount = bcsub($balance->amount, $dto->amount, 2);

        if ($balance->amount !== $ledgerRecord->after) {
            throw new BalanceLogicException('Error withdrawing money from balance', Response::HTTP_CONFLICT);
        }

        $ledgerRecord->save();
        $balance->save();

        DB::commit();

        $balance->load(['ledger_records' => function ($q) use ($ledgerRecord) {
            $q->where('id', $ledgerRecord->id);
        }]);

        return $balance;
    }

    /**
     * @throws Throwable
     * @throws BalanceLogicException
     */
    public function transfer(BalanceTransferDto $dto): Balance
    {
        DB::beginTransaction();

        $fromUser = User::findOrFail($dto->from_user_id);
        $toUser = User::findOrFail($dto->to_user_id);

        $fromBalance = $this->getOrCreateBalance($fromUser);
        $toBalance = $this->getOrCreateBalance($toUser);

        if (bccomp($dto->amount, $fromBalance->amount, 2) === 1) {
            throw new BalanceLogicException('Your account has insufficient funds to complete this transaction', Response::HTTP_CONFLICT);
        }

        $fromLedgerRecord = new LedgerRecord();
        $fromLedgerRecord->balance_id = $fromBalance->id;
        $fromLedgerRecord->to_balance_id = $toBalance->id;
        $fromLedgerRecord->comment = $dto->comment;
        $fromLedgerRecord->amount = $dto->amount;
        $fromLedgerRecord->operation = BalanceOperationTypeEnum::TransferOUT->value;
        $fromLedgerRecord->after = bcsub($fromBalance->amount, $dto->amount, 2);

        $fromBalance->amount = bcsub($fromBalance->amount, $dto->amount, 2);

        $toLedgerRecord = new LedgerRecord();
        $toLedgerRecord->balance_id = $toBalance->id;
        $toLedgerRecord->to_balance_id = $fromBalance->id;
        $toLedgerRecord->comment = $dto->comment;
        $toLedgerRecord->amount = $dto->amount;
        $toLedgerRecord->operation = BalanceOperationTypeEnum::TransferIN->value;
        $toLedgerRecord->after = bcadd($toBalance->amount, $dto->amount, 2);

        $toBalance->amount = bcadd($toBalance->amount, $dto->amount, 2);

        if ($toBalance->amount !== $toLedgerRecord->after || $fromBalance->amount !== $fromLedgerRecord->after) {
            throw new BalanceLogicException('Balance transfer error', Response::HTTP_CONFLICT);
        }

        $fromLedgerRecord->save();
        $toLedgerRecord->save();
        $fromBalance->save();
        $toBalance->save();

        DB::commit();

        $fromBalance->load(['ledger_records' => function ($q) use ($fromLedgerRecord) {
            $q->where('id', $fromLedgerRecord->id);
        }]);

        return $fromBalance;
    }

    public function getBalance(BalanceGetDto $dto): Balance
    {
        return $this->getOrCreateBalance($dto->user);
    }

    public function getOrCreateBalance(User $user): Balance
    {
        $balance = $user->balance()->first();

        if ($balance == null) {
            $balance = new Balance();
            $balance->user_id = $user->id;
            $balance->balance = 0.00;
            $balance->save();
        }

        return $balance;
    }
}
