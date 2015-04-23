<?php namespace Amazingsurge\Permissions\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreatePermissionsTable extends Migration
{

    public function up()
    {
        Schema::create('amazingsurge_permissions_permissions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('amazingsurge_permissions_permissions');
    }

}
