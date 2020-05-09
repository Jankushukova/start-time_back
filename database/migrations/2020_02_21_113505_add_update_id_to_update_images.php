<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdateIdToUpdateImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('update_images', function (Blueprint $table) {
            $table->bigInteger('update_id')->unsigned();
            $table->foreign('update_id','fk_images_updates')
                ->references('id')
                ->on('updates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('update_images', function (Blueprint $table) {
            $table->dropForeign('fk_images_updates');
            $table->dropColumn('update_id');
        });
    }
}
