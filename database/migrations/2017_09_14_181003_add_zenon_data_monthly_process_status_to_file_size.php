<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZenonDataMonthlyProcessStatusToFileSize extends Migration
{
    public $tableName = 'zenon_data_monthly_process_status';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'file_kb_size'))
            {
                $table->double('file_kb_size')
                        ->after('csv_file_name')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'file_kb_size'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('file_kb_size');
            });
        }

    }

}
