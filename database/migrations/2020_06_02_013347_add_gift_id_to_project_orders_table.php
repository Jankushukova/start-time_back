<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGiftIdToProjectOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_orders', function (Blueprint $table) {
            $table->bigInteger('gift_id')->unsigned()->nullable(true);
            $table->foreign('gift_id','fk_orders_gifts')
                ->references('id')
                ->on('project_gifts');
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
            $table->dropForeign('fk_orders_gifts');
            $table->dropColumn('gift_id');
        });
    }
}
