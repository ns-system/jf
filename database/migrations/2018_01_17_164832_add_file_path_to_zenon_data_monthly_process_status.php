<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFilePathToZenonDataMonthlyProcessStatus extends Migration
{

    public $tableName = 'zenon_data_monthly_process_status';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'file_path'))
            {
                $table->string('file_path')
                        ->after('error_message')
                ;
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'file_path'))
            {
                $table->dropColumn('file_path');
            }
        });
    }

}
