<?php namespace DenverArt\ActivityFields\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddAdditionalFields extends Migration {
    public function up()
    {
        Schema::table('dam_activity_fields', function($table)
        {
            $table->time('start_time')->nullable();
            $table->boolean('ticketed')->default(false);
        });
    }

    public function down()
    {
        Schema::table('dam_activity_fields', function($table) 
        {
            $table->dropColumn('start_time');
            $table->dropColumn('ticketed');
        });
    }
}