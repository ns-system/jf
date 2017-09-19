<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobStatus extends Migration
{

    public $tableName = 'job_status';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->boolean("is_copy_start");
            $table->boolean("is_copy_end");
            $table->boolean("is_copy_error");
            $table->boolean("is_import_start");
            $table->boolean("is_import_end");
            $table->boolean("is_import_error");
            $table->timestamps("");
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
