<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('paytm_withdraw_id')->nullable();
            $table->foreign('paytm_withdraw_id')->references('id')->on('withdrawal_requests');

            $table->unsignedBigInteger('mannual_withdraw_id')->nullable();
            $table->foreign('mannual_withdraw_id')->references('id')->on('mannual_transactions');

            $table->string('username');
            $table->decimal('transaction_amount', 10, 2, false);
            $table->boolean('dr_cr');
            $table->string('order_id')->nullable();
            $table->enum('transaction_type', [1, 2, 3, 4, 5, 6])->comment('1-add,2-withdraw,3-win,4-loss,5-referral_commission,6-penalty');
            $table->enum('status', [0, 1, 2, 3])->default(0)->comment('0-pending,1-successful,2-rejected,3-failed');
            $table->decimal('closing_balance', 10, 2, false);
            $table->string('game_image')->nullable();
            $table->string('opposition_player')->nullable();
            $table->string('battle_id')->nullable();
            $table->string('game_name')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_histories');
    }
}
