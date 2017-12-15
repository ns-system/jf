<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFilioparentalStateToChochikuAccountLedgers extends Migration
{

    public $tableName = 'chochiku_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'filioparental_state'))
            {
                $table->integer('filioparental_state')
                        ->default(0)
                        ->index()
                        ->after('key_account_number')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'filioparental_state'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('filioparental_state');
            });
        }
    }

}
