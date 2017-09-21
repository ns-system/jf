<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHolidays extends Migration
{
    public $tableName = 'holidays';
    public $connect   = 'mysql_sinren';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments('id');
            $table->date('holiday')->index();
            $table->string('holiday_name');
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
