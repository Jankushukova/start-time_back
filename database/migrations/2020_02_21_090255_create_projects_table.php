<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title_rus');
            $table->string('title_eng');
            $table->string('title_kz');
            $table->string('main_language')->default('rus');
            $table->text('description_rus')->nullable();
            $table->text('description_kz')->nullable();
            $table->text('description_eng')->nullable();
            $table->timestamp('deadline');
            $table->text('content_rus')->nullable();
            $table->text('content_kz')->nullable();
            $table->text('content_eng')->nullable();
            $table->string('video');
            $table->string('goal');
            $table->integer('views')->default(0);
            $table->bigInteger('gathered')->default(0);
            $table->boolean('active')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
