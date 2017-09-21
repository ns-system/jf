<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSinrenUsers extends Migration
{

    public $tableName = 'sinren_users';
    public $connect   = 'mysql_sinren';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('division_id');
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
