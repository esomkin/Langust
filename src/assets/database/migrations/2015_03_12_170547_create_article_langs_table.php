<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_langs', function(Blueprint $table){

            $table->increments('id');
            $table->string('name', 200);
            $table->string('title', 200);
            $table->integer('article_id')->unsigned();
            $table->enum('lang', [

                'en', 
                'fr',
                'es',
            ])->index();

            $table->unique([

                'article_id',
                'lang'
            ]);
            $table->foreign('article_id')
                ->references('id')
                ->on('articles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('article_langs');
    }
}
