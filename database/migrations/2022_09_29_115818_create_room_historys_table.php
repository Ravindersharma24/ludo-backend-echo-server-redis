<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomHistorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_historys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_id');
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->unsignedInteger('player_id');
            $table->foreign('player_id')->references('id')->on('users');
            $table->integer('game_id');
            $table->string('game_name');
            $table->string('player_name');
            $table->string('room_code');
            $table->decimal('entry_fees',10,2,false);
            $table->decimal('price',10,2,false);
            $table->decimal('admin_commission',10,2,false);
            $table->enum('player_shared_status', [0, 1, 2,3])->default(0)->comment('0-ongoing,1-win,2-loss,3-cancel');
            $table->enum('admin_provided_status', [0, 1, 2,3])->default(0)->comment('0-pending,1-win,2-loss,3-cancel');
            $table->enum('penalty_status', [0, 1])->default(0)->comment('0-not-applied,1-applied');
            $table->string('screenshot')->nullable()->default('notFound.png');
            $table->string('cancel_note')->nullable()->default('-');
            // $table->enum('status', [0, 1, 2])->default(0)->comment('0-omgoing,1-winner,2-loser');
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
        Schema::dropIfExists('room_historys');
    }
}
