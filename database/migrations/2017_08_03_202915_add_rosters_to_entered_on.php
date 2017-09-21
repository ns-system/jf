<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRostersToEnteredOn extends Migration
{
    public $tableName = 'rosters';
    public $connect   = 'mysql_roster';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'entered_on'))
            {
                $table->date('entered_on')
                        ->after('process_user_id')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'entered_on'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('entered_on');
            });
        }
    }
}
