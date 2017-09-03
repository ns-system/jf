<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkTypes extends Migration
{

    public $tableName = 'work_types';
    public $connect   = 'mysql_roster';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments('id');
            $table->integer('work_type_id');
            $table->string('work_type_name');
            $table->time('work_start_time');
            $table->time('work_end_time');
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
