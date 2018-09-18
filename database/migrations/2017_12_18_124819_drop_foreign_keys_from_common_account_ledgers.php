<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignKeysFromCommonAccountLedgers extends Migration
{

    public $tableName = 'common_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'filioparental_state'))
            {
                $table->dropColumn('filioparental_state');
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'filioparental_state'))
            {
                $table->integer('filioparental_state')
                        ->after('key_account_number')
                        ->index()
                ;
            }
        });
    }

}
