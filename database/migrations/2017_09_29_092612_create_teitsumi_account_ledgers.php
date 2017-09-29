<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeitsumiAccountLedgers extends Migration
{

    public $tableName = 'teitsumi_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->date("last_recorded_on")->nullable();
            $table->double("payment_balance");
            $table->double("transfer_payment_number");
            $table->float("interest_rate");
            $table->double("payment_amount");
            $table->date("deposited_on")->nullable();
            $table->integer("payment_date");
            $table->date("contract_maturity_on")->nullable();
            $table->date("fixed_maturity_on")->nullable();
            $table->double("profit_contract_amount");
            $table->double("profit_compensation_amount");
            $table->integer("term_code")->index();
            $table->integer("paid_count");
            $table->double("term_cumulation");
            $table->double("adjustment_amount");
            $table->integer("payment_state");
            $table->double("prepaid_discount_fee");
            $table->double("delay_interest");
            $table->double("prepaid_cumulation");
            $table->double("delay_cumulation");
            $table->integer("auto_cancellation");
            $table->date("modified_maturity_on")->nullable();
            $table->integer("bonus_date_1");
            $table->integer("bonus_date_2");
            $table->integer("bonus_date_3");
            $table->integer("bonus_date_4");
            $table->double("bonus_amount_1");
            $table->double("bonus_amount_2");
            $table->double("bonus_amount_3");
            $table->double("bonus_amount_4");
            $table->double("previous_teitsumi_number");
            $table->double("next_teitsumi_number");
            $table->integer("constant_deposit_contract_count");
            $table->integer("bonus_deposit_contract_count");
            $table->integer("regular_deposited_count");
            $table->integer("bonus_deposited_count");
            $table->date("next_regular_deposited_on")->nullable();
            $table->date("next_bonus_deposited_on")->nullable();
            $table->float("atm_extra_interest_rate");
            $table->char("spare_1", 179);
            $table->integer("monthly_id")->index();
            $table->integer("subject_code")->index();
            $table->integer("key_account_number")->index();
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
