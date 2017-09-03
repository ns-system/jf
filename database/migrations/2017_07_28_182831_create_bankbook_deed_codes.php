<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankbookDeedCodes extends Migration
{
    public $tableName = 'deposit_bankbook_deed_codes';
    public $connect   = 'mysql_master';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("bankbook_deed_code")->index();
            $table->integer("subject_code")->index();
            $table->string("bankbook_deed_name");
            $table->timestamps("");
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }
}
