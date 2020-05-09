<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewsIdToNewsLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news_likes', function (Blueprint $table) {
            $table->bigInteger('news_id')->unsigned();
            $table->foreign('news_id','fk_news_likes_news')
                ->references('id')
                ->on('news'); //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news_likes', function (Blueprint $table) {
            $table->dropForeign('fk_news_likes_news');
            $table->dropColumn('news_id');
        });
    }
}
