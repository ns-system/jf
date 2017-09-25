<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditZenonDataCsvFilesToCycle extends Migration
{

    public $tableName = 'zenon_data_csv_files';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {


            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'cycle'))
            {
                $table->string('cycle')
                        ->after('reference_return_date')
                ;
            }

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_daily'))
            {
                Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                    $table->dropColumn('is_daily');
                });
            }

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_monthly'))
            {
                Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                    $table->dropColumn('is_monthly');
                });
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_daily'))
            {
                $table->boolean('is_daily')
                        ->after('reference_return_date')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_monthly'))
            {
                $table->boolean('is_monthly')
                        ->after('is_daily')
                ;
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'cycle'))
            {
                $table->dropColumn('cycle');
            }
        });
    }

}
