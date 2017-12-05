<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsKeizaiToDepositGistCodes extends Migration
{

    public $tableName = 'deposit_gist_codes';
    public $connect   = 'mysql_master';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'is_keizai'))
            {
                $table->boolean('is_keizai')->index()->after('id');
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'is_keizai'))
            {
                $table->dropColumn('is_keizai');
            }
        });
    }

}
