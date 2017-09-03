<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditGistCodesName extends Migration
{

    public $tableName    = 'gist_codes';
    public $newTableName = 'deposit_gist_codes';
    public $connect      = 'mysql_master';

    public function up() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->rename($this->tableName, $this->newTableName);
        }
    }

    public function down() {
//        if (Schema::connection($this->connect)->hasTable($this->newTableName))
//        {
//            Schema::connection($this->connect)->rename($this->newTableName, $this->tableName);
//        }
    }

}
