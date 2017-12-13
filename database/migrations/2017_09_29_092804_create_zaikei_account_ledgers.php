<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZaikeiAccountLedgers extends Migration
{

    public $tableName = 'zaikei_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->double("bankbook_balance");
            $table->double("transfer_payment_number");
            $table->integer("auto_deposited_on");
            $table->integer("last_deposited_month");
            $table->double("auto_deposit_amount");
            $table->double("bonus_deposit_amount");
            $table->date("payment_started_on")->nullable();
            $table->date("next_executed_on")->nullable();
            $table->date("office_code")->nullable();
            $table->char("employee_number", 12);
            $table->integer("holiday_state");
            $table->date("bonus_on")->nullable();
            $table->double("jifuri_account_number");
            $table->integer("contract_process_month");
            $table->integer("office_name_state");
            $table->char("office_name", 30);
            $table->char("spare_1", 246);
            $table->integer("monthly_id")->index();
            $table->integer("subject_code")->index();
            $table->double("key_account_number")->index();
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
