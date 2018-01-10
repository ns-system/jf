<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepositAndLoanToZenonDataCsvFiles extends Migration
{

    public $tableName = 'zenon_data_csv_files';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_loan_split'))
            {
                $table->boolean('is_loan_split')
                        ->after('is_split')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_deposit_split'))
            {
                $table->boolean('is_deposit_split')
                        ->after('is_split')
                ;
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_deposit_split'))
            {
                $table->dropColumn('is_deposit_split');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_loan_split'))
            {
                $table->dropColumn('is_loan_split');
            }
        });
    }

}
