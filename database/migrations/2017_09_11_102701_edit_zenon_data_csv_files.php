<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditZenonDataCsvFiles extends Migration
{
    public $tableName = 'zenon_data_csv_files';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'identifier'))
            {
                $table->string('identifier')
                        ->after('id')
                        ->index()
                ;
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'csv_file_name'))
            {
                $table->dropColumn('csv_file_name');
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'identifier'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('identifier');
            });
        }
        if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'csv_file_name'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->string('csv_file_name');
            });
        }
    }
}
