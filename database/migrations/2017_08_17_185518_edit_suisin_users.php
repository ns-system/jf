<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSuisinUsers extends Migration
{

    public $tableName = 'suisin_users';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'email'))
            {
                Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                    $table->dropColumn('email');
                });
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'division'))
            {
                Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                    $table->dropColumn('division');
                });
            }

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'user_id'))
            {
                $table->integer('user_id')
                        ->after('id')
                        ->index()
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'user_id'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }

}
