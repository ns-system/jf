<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZenonDataMonthlyProcessStatusToErrorMessage extends Migration
{

    public $tableName = 'zenon_data_monthly_process_status';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'error_message'))
            {
                $table->string('error_message')->after('id');
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'job_status_id'))
            {
                $table->integer('job_status_id')->index()->after('id');
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'error_message'))
            {
                $table->dropColumn('error_message');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'job_status_id'))
            {
                $table->dropColumn('job_status_id');
            }
        });
    }

}
