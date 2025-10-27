<?php

namespace Tests\Unit\app\Services\Balance;

use App\Exceptions\BalanceLogicException;
use App\Models\User;
use App\Services\Balance\Dto\BalanceDepositDto;
use App\Services\Balance\Dto\BalanceGetDto;
use App\Services\Balance\Dto\BalanceTransferDto;
use App\Services\Balance\Dto\BalanceWithdrawDto;
use App\Services\Balance\Http\BalanceService;
use Database\Seeders\BalanceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;

    protected User $user2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            BalanceSeeder::class,
        ]);

        $this->user1 = User::query()->find(1);
        $this->user2 = User::query()->find(2);
    }

    public function test_deposit() {
        $dto = new BalanceDepositDto([
            'user_id' => $this->user1->id,
            'amount' => "100.01",
            'comment' => "some comment",
        ]);

        $balanceService = $this->app->make(BalanceService::class);

        $balance = $balanceService->deposit($dto);

        $this->assertEquals("1100.01", $balance->amount);

        $this->assertEquals($dto->comment, $balance->ledger_records->first()->comment);
    }

    public function test_withdraw()
    {
        $dto = new BalanceWithdrawDto([
            'user_id' => $this->user1->id,
            'amount' => "100.01",
            'comment' => "some comment",
        ]);

        $balanceService = $this->app->make(BalanceService::class);

        $balance = $balanceService->withdraw($dto);

        $this->assertEquals("899.99", $balance->amount);

        $this->assertEquals($dto->comment, $balance->ledger_records->first()->comment);
    }

    public function test_withdraw_without_balance() {
        $dto = new BalanceWithdrawDto([
            'user_id' => $this->user2->id,
            'amount' => "0.01",
            'comment' => "some comment",
        ]);

        $balanceService = $this->app->make(BalanceService::class);

        $this->expectException(BalanceLogicException::class);
        $this->expectExceptionMessage('Your account has insufficient funds to complete this transaction');

        $balanceService->withdraw($dto);
    }

    public function test_transfer()
    {
        $dto = new BalanceTransferDto([
            'from_user_id' => $this->user1->id,
            'to_user_id' => $this->user2->id,
            'amount' => "0.01",
            'comment' => "transfer operation",
        ]);

        $balanceService = $this->app->make(BalanceService::class);

        $balance = $balanceService->transfer($dto);

        $this->assertEquals("999.99", $balance->amount);
        $this->assertEquals($dto->comment, $balance->ledger_records->first()->comment);
        $this->assertEquals($this->user2->id, $balance->ledger_records->first()->toBalance()->first()->user_id);
    }

    public function test_transfer_without_balance()
    {
        $dto = new BalanceTransferDto([
            'from_user_id' => $this->user2->id,
            'to_user_id' => $this->user1->id,
            'amount' => "0.01",
            'comment' => "transfer operation without balance",
        ]);

        $balanceService = $this->app->make(BalanceService::class);

        $this->expectException(BalanceLogicException::class);
        $this->expectExceptionMessage('Your account has insufficient funds to complete this transaction');

        $balanceService->transfer($dto);
    }

    public function test_get_balance()
    {
        $dto = new BalanceGetDto([
            'user' => $this->user1
        ]);

        $balanceService = $this->app->make(BalanceService::class);

        $balance = $balanceService->getBalance($dto);

        $this->assertEquals("1000.00", $balance->amount);
    }

    public function test_get_balance_without_balance()
    {
        $dto = new BalanceGetDto([
            'user' => $this->user2
        ]);

        $balanceService = $this->app->make(BalanceService::class);

        $balance = $balanceService->getBalance($dto);

        $this->assertEquals("0.00", $balance->amount);
    }

    public function test_get_or_create_balance()
    {
        $balanceService = $this->app->make(BalanceService::class);

        $balance = $balanceService->getOrCreateBalance($this->user1);

        $this->assertEquals("1000.00", $balance->amount);
    }

    public function test_get_or_create_balance_without_balance()
    {
        $balanceService = $this->app->make(BalanceService::class);

        $balance = $balanceService->getOrCreateBalance($this->user2);

        $this->assertEquals("0.00", $balance->amount);
    }
}
