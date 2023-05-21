<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Create1555355612782UsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('user_image')->nullable()->default('Avatar8.png');;
            $table->string('phone_no')->unique();
            $table->string('otp');
            $table->string('email');
            $table->datetime('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->boolean('active')->default(true);
            $table->float('balance', 10, 2)->default(0);
            $table->float('deposit_cash', 10, 2)->default(0);
            $table->float('winning_cash', 10, 2)->default(0);
            $table->float('refer_cash', 10, 2)->default(0);
            // $table->string('referred_by')->nullable()->index();
            $table->string('referred_by');
            $table->float('created_battles', 10, 2)->default(0);
            $table->string('affiliate_id')->unique();
            $table->string('upi_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
    }
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
