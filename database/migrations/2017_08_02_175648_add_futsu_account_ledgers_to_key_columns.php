<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFutsuAccountLedgersToKeyColumns extends Migration
{

    public $tableName = 'futsu_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'subject_code'))
            {
                $table->integer('subject_code')
                        ->after('id')
                        ->index()
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'key_account_number'))
            {
                $table->integer('key_account_number')
                        ->after('monthly_id')
                        ->index()
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'subject_code'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('subject_code');
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'key_account_number'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('key_account_number');
            });
        }
    }

}
