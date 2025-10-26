<?php

namespace App\Http\Controllers\Api\v1\Balance;

use App\Exceptions\BalanceLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Balance\DepositBalanceRequest;
use App\Http\Requests\v1\Balance\TransferBalanceRequest;
use App\Http\Requests\v1\Balance\WithdrawBalanceRequest;
use App\Http\Resources\v1\Balance\BalanceDepositResource;
use App\Http\Resources\v1\Balance\BalanceResource;
use App\Http\Resources\v1\Balance\BalanceTransferResource;
use App\Http\Resources\v1\Balance\BalanceWithdrawResource;
use App\Models\User;
use App\Services\Balance\Dto\BalanceDepositDto;
use App\Services\Balance\Dto\BalanceGetDto;
use App\Services\Balance\Dto\BalanceTransferDto;
use App\Services\Balance\Dto\BalanceWithdrawDto;
use App\Services\Balance\Http\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BalanceController extends Controller
{
    public function __construct
    (
        private readonly BalanceService $balanceService
    )
    {
    }

    /**
     * @throws Throwable
     * @throws BalanceLogicException
     */
    public function deposit(DepositBalanceRequest $request): JsonResponse
    {
        $dto = new BalanceDepositDto($request->validated());

        $balance = $this->balanceService->deposit($dto);

        return (new BalanceDepositResource($balance))->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws Throwable
     * @throws BalanceLogicException
     */
    public function withdraw(WithdrawBalanceRequest $request): JsonResponse
    {
        $dto = new BalanceWithdrawDto($request->validated());

        $balance = $this->balanceService->withdraw($dto);

        return (new BalanceWithdrawResource($balance))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function transfer(TransferBalanceRequest $request): JsonResponse
    {
        $dto = new BalanceTransferDto($request->validated());

        $balance = $this->balanceService->transfer($dto);

        return (new BalanceTransferResource($balance))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function getBalance(User $user, Request $request): JsonResponse
    {
        $dto = new BalanceGetDto(['user' => $user]);

        $balance = $this->balanceService->getBalance($dto);

        return (new BalanceResource($balance))->response()->setStatusCode(Response::HTTP_OK);
    }
}
