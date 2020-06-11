<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('update', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title_eng')->nullable();
            $table->string('title_rus')->nullable();
            $table->string('title_kz')->nullable();
            $table->text('description_rus')->nullable();
            $table->text('description_eng')->nullable();
            $table->text('description_kz')->nullable();
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
        Schema::dropIfExists('update');
    }
}
