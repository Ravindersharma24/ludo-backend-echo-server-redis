<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('commission_type', [1, 2])->comment('1-percentage,2-amount');
            $table->decimal('from_amount', 10, 2, false);
            $table->decimal('to_amount', 10, 2, false);
            $table->decimal('commission_value', 10, 2, false);
            $table->enum('condition', [1, 2, 3])->comment('1-lessThan,2-greaterThan,3-between');
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
        Schema::dropIfExists('admin_commissions');
    }
}
