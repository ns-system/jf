<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRosterUsers extends Migration
{

    public $tableName = 'roster_users';
    public $connect   = 'mysql_roster';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
//            $table->boolean('is_administrator');
            $table->boolean('is_chief');
            $table->boolean('is_proxy');
            $table->boolean('is_proxy_active');
            $table->integer('work_type_id');
            $table->timestamps('');
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
