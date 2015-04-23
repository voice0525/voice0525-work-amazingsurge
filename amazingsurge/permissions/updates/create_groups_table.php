<?php namespace Amazingsurge\Permissions\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateGroupsTable extends Migration
{

    public function up()
    {
        Schema::create('amazingsurge_permissions_groups', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('amazingsurge_permissions_groups');
    }

}
