<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRostersToCreateUserInformation extends Migration
{

    public $tableName = 'rosters';
    public $connect   = 'mysql_roster';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'create_user_id'))
            {
                $table->integer('create_user_id')
                        ->after('reject_reason')
                        ->index()
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'edit_user_id'))
            {
                $table->integer('edit_user_id')
                        ->after('create_user_id')
                        ->index()
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'create_user_id'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('create_user_id');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'edit_user_id'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('edit_user_id');
            });
        }
    }

}
