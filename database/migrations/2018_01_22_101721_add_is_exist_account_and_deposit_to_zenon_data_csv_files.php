<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsExistAccountAndDepositToZenonDataCsvFiles extends Migration
{

    public $tableName = 'zenon_data_csv_files';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_exist_account_and_deposit'))
            {
                $table->boolean('is_exist_account_and_deposit')
                        ->after('is_account_convert')
                ;
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_exist_account_and_deposit'))
            {
                $table->dropColumn('is_exist_account_and_deposit');
            }
        });
    }

}
