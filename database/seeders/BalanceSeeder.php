<?php

namespace Database\Seeders;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Balance::truncate();

        Balance::create([
            'user_id' => User::query()->first()->id,
            'balance' => 100000 // 1000.00
        ]);
    }
}
