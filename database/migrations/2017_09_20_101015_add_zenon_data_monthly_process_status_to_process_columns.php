<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZenonDataMonthlyProcessStatusToProcessColumns extends Migration
{

    public $tableName = 'zenon_data_monthly_process_status';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_process_end'))
            {
                $table->boolean('is_pre_process_end')
                        ->after('is_pre_process')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_process_error'))
            {
                $table->boolean('is_pre_process_error')
                        ->after('is_pre_process_end')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_post_process_end'))
            {
                $table->boolean('is_post_process_end')
                        ->after('is_post_process')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_post_process_error'))
            {
                $table->boolean('is_post_process_error')
                        ->after('is_post_process_end')
                ;
            }

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_process'))
            {
                $table->renameColumn('is_pre_process', 'is_pre_process_start');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_post_process'))
            {
                $table->renameColumn('is_post_process', 'is_post_process_start');
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_process_end'))
            {
                $table->dropColumn('is_pre_process_end');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_process_error'))
            {
                $table->dropColumn('is_pre_process_error');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_post_process_error'))
            {
                $table->dropColumn('is_post_process_error');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_process_start'))
            {
                $table->renameColumn('is_pre_process_start', 'is_pre_process');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_post_process_start'))
            {
                $table->renameColumn('is_post_process_start', 'is_post_process');
            }
        });
    }

}
