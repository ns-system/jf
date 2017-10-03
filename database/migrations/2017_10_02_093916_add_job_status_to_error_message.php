<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJobStatusToErrorMessage extends Migration
{

    public $tableName = 'job_status';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'error_message'))
            {
                $table->string('error_message')->after('id');
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'error_message'))
            {
                $table->dropColumn('error_message');
            }
        });
    }

}
