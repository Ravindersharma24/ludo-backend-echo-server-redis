<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBattlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('game_listing_id');
            $table->foreign('game_listing_id')->references('id')->on('gamelistings');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->decimal('price',10,2,false);
            $table->decimal('entry_fees',10,2,false);
            $table->decimal('admin_commission',10,2,false);
            $table->enum('battle_status',[0, 1, 2])->default(0)->comment('0-pending,1-in-progress,2-completed');
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
        Schema::dropIfExists('battles');
    }
}
