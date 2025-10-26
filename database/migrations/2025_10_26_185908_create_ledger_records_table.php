<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ledger_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('balance_id');
            $table->bigInteger('amount');
            $table->string('operation');
            $table->text('comment')->nullable();
            $table->bigInteger('balance_after')->nullable();
            $table->unsignedBigInteger('to_balance_id')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            if (!app()->environment('testing')) {
                $table->foreign('balance_id')->references('id')->on('balances')->onDelete('cascade');
                $table->foreign('to_balance_id')->references('id')->on('balances')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_records');
    }
};
