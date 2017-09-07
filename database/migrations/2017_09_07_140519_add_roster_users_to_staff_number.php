<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRosterUsersToStaffNumber extends Migration
{
    public $tableName = 'roster_users';
    public $connect   = 'mysql_roster';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'staff_number'))
            {
                $table->integer('staff_number')
                        ->after('user_id')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'staff_number'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('staff_number');
            });
        }
    }
}
