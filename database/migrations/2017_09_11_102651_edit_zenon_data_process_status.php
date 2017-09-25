<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditZenonDataProcessStatus extends Migration
{

    public $tableName = 'zenon_data_monthly_process_status';
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
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'csv_file_name'))
            {
                $table->dropColumn('csv_file_name');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'csv_file_set_on'))
            {
                $table->dropColumn('csv_file_set_on');
            }
        });
    }

}
