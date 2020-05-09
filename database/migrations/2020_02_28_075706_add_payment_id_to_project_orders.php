<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentIdToProjectOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_orders', function (Blueprint $table) {
            $table->bigInteger('payment_id')->unsigned();
            $table->foreign('payment_id','fk_project_orders_payments')
                ->references('id')
                ->on('payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_orders', function (Blueprint $table) {
            $table->dropForeign('fk_project_orders_payments');
            $table->dropColumn('payment_id');

        });
    }
}
