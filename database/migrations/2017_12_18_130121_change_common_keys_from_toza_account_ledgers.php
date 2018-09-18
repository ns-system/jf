<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCommonKeysFromTozaAccountLedgers extends Migration
{

    public $tableName = 'toza_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'filioparental_state'))
            {
                $table->dropColumn('filioparental_state');
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'common_id'))
            {
                $table->integer('common_id')
                        ->unsigned()
                        ->index()
                        ->after('id')
                ;
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
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'common_id'))
            {
                $table->dropColumn('common_id');
            }
        });
    }

}
