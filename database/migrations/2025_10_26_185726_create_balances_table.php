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
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('balance')->default(0);
            $table->timestamps();

            if (!app()->environment('testing')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });

        if (!app()->environment('testing')) {
            DB::statement('ALTER TABLE balances ADD CONSTRAINT balance_non_negative CHECK (balance >= 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};
