<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersToFirstNameAndLastName extends Migration
{

    public $tableName = 'users';
    public $connect   = 'mysql_laravel';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'first_name'))
            {
                $table->string('first_name')->index()->after('id');
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'last_name'))
            {
                $table->string('last_name')->index()->after('first_name');
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'first_name_kana'))
            {
                $table->string('first_name_kana')->index()->after('last_name');
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'last_name_kana'))
            {
                $table->string('last_name_kana')->index()->after('first_name_kana');
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'first_name'))
            {
                $table->dropColumn('first_name');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'last_name'))
            {
                $table->dropColumn('last_name');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'first_name_kana'))
            {
                $table->dropColumn('first_name_kana');
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'last_name_kana'))
            {
                $table->dropColumn('last_name_kana');
            }
        });
    }

}
