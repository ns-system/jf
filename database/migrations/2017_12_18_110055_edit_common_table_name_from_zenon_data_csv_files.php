<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCommonTableNameFromZenonDataCsvFiles extends Migration
{

    public $tableName = 'zenon_data_csv_files';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            for ($i = 1; $i <= 4; $i++) {
                if (Schema::connection($this->connect)->hasColumn($this->tableName, 'split_foreign_key_' . $i))
                {
                    $table->dropColumn('split_foreign_key_' . $i)
                    ;
                }
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'common_table_name'))
            {
                $table->string('common_table_name')
                        ->after('table_name')
                ;
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            for ($i = 4; $i >= 1; $i--) {
                if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'split_foreign_key_' . $i))
                {
                    $table->string('split_foreign_key_' . $i)
                            ->after('subject_column_name')
                    ;
                }
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'common_table_name'))
            {
                $table->dropColumn('common_table_name');
            }
        });
    }

}
