<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectCommentIdToCommentLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comment_likes', function (Blueprint $table) {
            $table->bigInteger('project_comment_id')->unsigned()->nullable(true);
            $table->foreign('project_comment_id','fk_likes_project_comment')
                ->references('id')
                ->on('project_comments');
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
            $table->dropForeign('fk_likes_project_comment');
            $table->dropColumn('project_comment_id');        });
    }
}
