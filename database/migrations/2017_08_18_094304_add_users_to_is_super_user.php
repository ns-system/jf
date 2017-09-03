<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersToIsSuperUser extends Migration
{
    public $tableName = 'users';
    public $connect   = 'mysql_laravel';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_super_user'))
            {
                $table->boolean('is_super_user')
                        ->after('unencrypt_password')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_super_user'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('is_super_user');
            });
        }
    }
}
