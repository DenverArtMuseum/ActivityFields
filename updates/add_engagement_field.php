<?php namespace DenverArt\ActivityFields\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddEngagementField extends Migration {
    public function up()
    {
        Schema::table('dam_activity_fields', function($table)
        {
            $table->string('engagement')->nullable;
        });
    }

    public function down()
    {
        Schema::table('dam_activity_fields', function($table) 
        {
            $table->dropColumn('engagement');
        });
    }
}