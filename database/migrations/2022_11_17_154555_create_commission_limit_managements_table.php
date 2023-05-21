<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionLimitManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_limit_managements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('refer_commission_percentage',10,2,false);
            $table->decimal('wallet_withdraw_limit',10,2,false);
            $table->decimal('refer_reedem_limit',10,2,false);
            $table->decimal('max_refer_commission',10,2,false);
            $table->decimal('pending_game_penalty_amt',10,2,false);
            $table->decimal('wrong_result_penalty_amt',10,2,false);
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
        Schema::dropIfExists('commission_limit_managements');
    }
}
