<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJapaneseColumnNameToZenonTableColumnConfigs extends Migration
{

    public $tableName = 'zenon_table_column_configs';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'japanese_column_name'))
            {
                $table->string('japanese_column_name')
                        ->after('column_name')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'japanese_column_name'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('japanese_column_name');
            });
        }
    }

}
