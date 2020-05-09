<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewsCommentIdToCommentLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comment_likes', function (Blueprint $table) {
            $table->bigInteger('news_comment_id')->unsigned()->nullable(true);
            $table->foreign('news_comment_id','fk_likes_news_comment')
                ->references('id')
                ->on('news_comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comment_likes', function (Blueprint $table) {
            $table->dropForeign('fk_likes_news_comment');
            $table->dropColumn('news_comment_id');
        });
    }
}
