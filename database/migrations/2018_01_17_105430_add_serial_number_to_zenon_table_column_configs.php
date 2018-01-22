<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerialNumberToZenonTableColumnConfigs extends Migration
{

    public $tableName = 'zenon_table_column_configs';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'serial_number'))
            {
                $table->integer('serial_number')
                        ->index()
                        ->after('id')
                ;
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'serial_number'))
            {
                $table->dropColumn('serial_number');
            }
        });
    }

}
