<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanFundAuxiliaryCodes extends Migration
{
    public $tableName = 'loan_fund_auxiliary_codes';
    public $connect   = 'mysql_master';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("fund_auxiliary_code")->index();
            $table->string("fund_auxiliary_category");
            $table->string("fund_auxiliary_name", 255);
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