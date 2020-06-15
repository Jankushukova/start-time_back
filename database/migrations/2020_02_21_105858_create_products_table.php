<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title_eng');
            $table->string('title_rus');
            $table->string('title_kz');
            $table->text('description_eng')->nullable();
            $table->text('description_rus')->nullable();
            $table->text('description_kz')->nullable();
            $table->boolean('active')->default(false);
            $table->string('cost');
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
