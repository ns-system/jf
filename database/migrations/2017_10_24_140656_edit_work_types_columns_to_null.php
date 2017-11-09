<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditWorkTypesColumnsToNull extends Migration
{

    public $tableName = 'work_types';
    public $connect   = 'mysql_roster';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'work_start_time'))
            {
                $table->time('work_start_time')->nullable()->change();
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'work_end_time'))
            {
                $table->time('work_end_time')->nullable()->change();
            }
        });
    }

    public function down() {
        
    }

}
