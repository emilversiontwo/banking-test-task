<?php

namespace Feature\app\Http\Controllers\Balance;

use App\Models\User;
use Database\Seeders\BalanceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BalanceControllerTest extends TestCase
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


    public function test_get_balance()
    {
        $response = $this->getJson('/api/v1/balance/' . $this->user1->id);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.amount', "1000.00");
    }

    public function test_get_balance_without_balance()
    {
        $response = $this->getJson('/api/v1/balance/' . $this->user2->id);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.amount', "0.00");
    }

    public function test_transfer()
    {
        $requestBody = [
            'from_user_id' => $this->user1->id,
            'to_user_id' => $this->user2->id,
            'amount' => 100.00,
            'comment' => 'some comment'
        ];

        $response = $this->postJson('/api/v1/transfer/', $requestBody);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.amount', "900.00");

        $this->assertDatabaseHas('balances', [
            'balance' => 10000, //100.00
        ]);
        $this->assertDatabaseHas('balances', [
            'balance' => 90000, //900.00
        ]);
    }

    public function test_transfer_without_balance()
    {
        $requestBody = [
            'from_user_id' => $this->user2->id,
            'to_user_id' => $this->user1->id,
            'amount' => 100.00,
            'comment' => 'attempt transfer'
        ];

        $response = $this->postJson('/api/v1/transfer/', $requestBody);

        $response->assertStatus(Response::HTTP_CONFLICT);
    }

    public function test_withdraw()
    {
        $requestBody = [
            'user_id' => $this->user1->id,
            'amount' => 100.01,
            'comment' => 'test payment'
        ];

        $response = $this->postJson('/api/v1/withdraw/',$requestBody);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.amount', "899.99");
    }

    public function test_withdraw_without_balance()
    {
        $requestBody = [
            'user_id' => $this->user2->id,
            'amount' => 100.03,
            'comment' => 'test payment'
        ];

        $response = $this->postJson('/api/v1/withdraw/',$requestBody);

        $response->assertStatus(Response::HTTP_CONFLICT);
    }

    public function test_deposit()
    {
        $requestBody = [
            'user_id' => $this->user1->id,
            'amount' => 100.99,
            'comment' => 'test deposit money'
        ];

        $response = $this->postJson('/api/v1/deposit/',$requestBody);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.amount', "1100.99");

        $requestBody = [
            'user_id' => $this->user1->id,
            'amount' => 0.01,
            'comment' => 'test2 deposit money'
        ];

        $response = $this->postJson('/api/v1/deposit/',$requestBody);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.amount', "1101.00");
    }

    public function test_deposit_without_balance()
    {
        $requestBody = [
            'user_id' => $this->user2->id,
            'amount' => 0.11,
            'comment' => 'test'
        ];

        $response = $this->postJson('/api/v1/deposit/',$requestBody);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.amount', "0.11");

        $requestBody = [
            'user_id' => $this->user2->id,
            'amount' => 0.01,
            'comment' => 'test2'
        ];

        $response = $this->postJson('/api/v1/deposit/',$requestBody);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.amount', "0.12");
    }
}
