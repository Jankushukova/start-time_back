<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdToProductPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_payments', function (Blueprint $table) {
            $table->bigInteger('order_id')->unsigned()->nullable(true);
            $table->foreign('order_id','fk_payment_orders')
                ->references('id')
                ->on('project_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_payments', function (Blueprint $table) {
            $table->dropForeign('fk_payment_orders');
            $table->dropColumn('order_id');
        });
    }
}
