<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewBetsudanAccountLedgers extends Migration
{

    public $tableName = 'new_betsudan_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->date("last_recorded_on");
            $table->double("surface_balance");
            $table->double("other_bank_ticket_amount");
            $table->date("last_transfered_on")->nullable();
            $table->double("past_month_surface_balance");
            $table->float("interest_rate");
            $table->double("bankbook_balance");
            $table->double("transfer_payment_number");
            $table->double("expected_interest");
            $table->double("monthly_deposit_amount");
            $table->integer("jifuri_date");
            $table->float("special_interest_rate");
            $table->integer("settlement_state");
            $table->date("next_settlement_on")->nullable();
            $table->date("specified_settlement_1_on")->nullable();
            $table->date("specified_settlement_2_on")->nullable();
            $table->date("specified_settlement_3_on")->nullable();
            $table->date("specified_settlement_4_on")->nullable();
            $table->char("spare_1");
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
