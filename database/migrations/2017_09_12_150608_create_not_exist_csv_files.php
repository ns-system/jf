<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotExistCsvFiles extends Migration
{

    public $tableName = 'not_exist_csv_files';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments('id');
            $table->string('csv_file_name');
            $table->integer('file_size');
            $table->date('csv_file_set_on');
            $table->boolean('is_monthly');
            $table->boolean('is_daily');
            $table->integer('monthly_id')->index();
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
