<?php namespace DenverArt\ActivityFields\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateActivityFieldsTable extends Migration
{

    public function up()
    {
        Schema::create('dam_activity_fields', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('activity_id');
            $table->foreign('activity_id')
                ->references('id')
                ->on('dma_friends_activities')
                ->onDelete('cascade');
            $table->string('duration')->nullable;
            $table->string('location')->nullable;

            $table->index('activity_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dam_activity_fields');
    }

}
