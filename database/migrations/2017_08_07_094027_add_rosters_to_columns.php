<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRostersToColumns extends Migration
{

    public $tableName = 'rosters';
    public $connect   = 'mysql_roster';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_plan_entry'))
            {
                $table->boolean('is_plan_entry')
                        ->after('user_id')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_actual_entry'))
            {
                $table->boolean('is_actual_entry')
                        ->after('is_plan_entry')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'reject_reason'))
            {
                $table->string('reject_reason')
                        ->after('process_user_id')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'plan_entered_at'))
            {
                $table->string('plan_entered_at')
                        ->after('month_id')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'actual_entered_at'))
            {
                $table->string('actual_entered_at')
                        ->after('plan_entered_at')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_plan_entry'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_plan_entry');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_actual_entry'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_actual_entry');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'reject_reason'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('reject_reason');
            });
        }
    }

}
