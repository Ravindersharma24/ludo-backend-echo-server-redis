<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('battle_id')->nullable();
            $table->unsignedInteger('game_id');
            $table->foreign('game_id')->references('id')->on('gamelistings');
            $table->string('code');
            $table->enum('status', [0, 1, 2, 3, 4])->default(0)->comment('0-open,1-waiting,2-closed,3-conflict,4-cancel'); // 0- open , 1- waiting, 2- closed, 3-conflict, 4-cancel
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
        Schema::dropIfExists('rooms');
    }
}
