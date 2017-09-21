<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersToUnencryptPassword extends Migration
{
    public $tableName = 'users';
    public $connect   = 'mysql_laravel';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'unencrypt_password'))
            {
                $table->text('unencrypt_password')
                        ->after('password')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'unencrypt_password'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('unencrypt_password');
            });
        }
    }
}
