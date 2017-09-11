<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditZenonDataProcessStatus extends Migration
{

    public $tableName = 'zenon_data_process_status';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'csv_file_name'))
            {
                $table->string('csv_file_name')
                        ->after('id')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'csv_file_set_on'))
            {
                $table->date('csv_file_set_on')
                        ->after('monthly_id')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'csv_file_name'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('csv_file_name');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'csv_file_set_on'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('csv_file_set_on');
            });
        }
    }

}
