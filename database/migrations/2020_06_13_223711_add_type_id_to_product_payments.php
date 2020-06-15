<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeIdToProductPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_payments', function (Blueprint $table) {
            $table->bigInteger('type_id')->unsigned()->nullable(true);
            $table->foreign('type_id','fk_payment_types')
                ->references('id')
                ->on('payment_types');
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
            $table->dropForeign('fk_payment_types');
            $table->dropColumn('type_id');
        });
    }
}
