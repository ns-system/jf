<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZenonDataMonthlyProcessFileToCsvFileName extends Migration
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
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_processing'))
            {
                $table->boolean('is_import_process')
                        ->after('zenon_data_csv_file_id')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_import_process'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_import_process');
            });
        }
    }

}
