<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZenonDataMonthlyProcessFilesToIsProcessEnd extends Migration
{

    public $tableName = 'zenon_data_monthly_process_status';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {


            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_process'))
            {
                $table->boolean('is_pre_process')
                        ->after('zenon_data_csv_file_id')
                ;
            }

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_post_process'))
            {
                $table->boolean('is_post_process')
                        ->after('is_pre_process')
                ;
            }



            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_process_end'))
            {
                $table->boolean('is_process_end')
                        ->after('is_post_process')
                ;
            }

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_execute'))
            {
                $table->boolean('is_execute')
                        ->after('zenon_data_csv_file_id')
                ;
            }

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_import_process'))
            {
                $table->dropColumn('is_import_process');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_check'))
            {
                $table->dropColumn('is_pre_check');
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_process'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_pre_process');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_post_process'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_post_process');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_process_end'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_process_end');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_execute'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_execute');
            });
        }

        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_import_process'))
            {
                $table->boolean('is_import_process')
                        ->after('zenon_data_csv_file_id')
                ;
            }

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_check'))
            {
                $table->boolean('is_pre_check')
                        ->after('is_import_process')
                ;
            }
        });
    }

}
