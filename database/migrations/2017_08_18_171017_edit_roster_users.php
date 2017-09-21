<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditRosterUsers extends Migration
{

    public $tableName = 'roster_users';
    public $connect   = 'mysql_roster';

    public function up() {

        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_administrator'))
            {
                $table->boolean('is_administrator')
                        ->after('user_id')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_administrator'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_administrator');
            });
        }
    }

}
