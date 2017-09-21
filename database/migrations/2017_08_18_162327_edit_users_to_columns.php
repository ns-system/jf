<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditUsersToColumns extends Migration
{

    public $tableName = 'users';
    public $connect   = 'mysql_laravel';

    public function up() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_administrator'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_administrator');
            });
        }
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'user_icon'))
            {
                $table->string('user_icon')
                        ->after('is_super_user')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'user_icon'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('user_icon');
            });
        }
    }

}
