<?php namespace DenverArt\ActivityFields\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateRatingTable extends Migration
{

    public function up()
    {
        Schema::create('dam_rating', function($table)
        {
            $table->integer('activity_id')
                ->unsigned()
                ->index();
            $table->foreign('activity_id')
                ->references('id')
                ->on('dma_friends_activities')
                ->onDelete('cascade');
            $table->integer('user_id')
                ->unsigned()
                ->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dam_rating');
    }

}
