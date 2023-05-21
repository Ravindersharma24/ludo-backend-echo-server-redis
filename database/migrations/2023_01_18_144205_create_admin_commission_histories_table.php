<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminCommissionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_commission_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('battle_id');
            $table->string('game_name');
            $table->bigInteger('room_id');
            $table->string('room_code');
            $table->decimal('entry_fees',10,2,false);
            $table->decimal('price',10,2,false);
            $table->decimal('admin_commission',10,2,false);
            $table->enum('transaction_type', [1,2])->comment('1-add,2-withdraw');
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
        Schema::dropIfExists('admin_commission_histories');
    }
}
