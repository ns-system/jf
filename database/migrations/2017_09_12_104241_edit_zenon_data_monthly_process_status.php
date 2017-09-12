<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditZenonDataMonthlyProcessStatus extends Migration
{

    public $tableName = 'zenon_data_monthly_process_status';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_check'))
            {
                $table->boolean('is_pre_check')
                        ->after('is_import')
                        ->index()
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'row_count'))
            {
                $table->integer('row_count')
                        ->after('is_pre_check')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'executed_row_count'))
            {
                $table->integer('executed_row_count')
                        ->after('row_count')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'process_started_at'))
            {
                $table->dateTime('process_started_at')
                        ->after('executed_row_count')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'process_ended_at'))
            {
                $table->dateTime('process_ended_at')
                        ->after('process_started_at')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_pre_check'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_pre_check');
            });
        }

        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'row_count'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('row_count');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'executed_row_count'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('executed_row_count');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'process_started_at'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('process_started_at');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'process_ended_at'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('process_ended_at');
            });
        }
    }

}
