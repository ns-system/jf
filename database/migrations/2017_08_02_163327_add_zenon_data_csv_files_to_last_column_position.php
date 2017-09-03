<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZenonDataCsvFilesToLastColumnPosition extends Migration
{
    public $tableName = 'zenon_data_csv_files';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'last_column_position'))
            {
                $table->integer('last_column_position')
                        ->after('first_column_position')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'last_column_position'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('last_column_position');
            });
        }
    }
}
