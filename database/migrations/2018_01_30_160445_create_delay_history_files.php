<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelayHistoryFiles extends Migration
{

    public $tableName = 'delay_history_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("create_month");
            $table->integer("prefecture_code")->index();
            $table->integer("organization_code")->index();
            $table->integer("store_number")->index();
            $table->integer("small_store_number")->index();
            $table->integer("customer_number")->index();
            $table->integer("subject_code")->index();
            $table->double("loan_account_number")->index();
            $table->integer("loan_category")->index();
            $table->integer("fund_state")->index();
            $table->integer("assist_fund_code")->index();
            $table->integer("capital_fund_state")->index();
            $table->integer("fund_usage_2")->index();
            $table->integer("fund_usage_1")->index();
            $table->integer("danshin_state")->index();
            $table->integer("collateral_state")->index();
            $table->integer("society_system_state")->index();
            $table->integer("security_person_state")->index();
            $table->integer("is_interest_supply")->index();
            $table->integer("disable_interest_supply")->index();
            $table->integer("special_repayment")->index();
            $table->integer("interest_payment_type")->index();
            $table->integer("delay_damage_target_state")->index();
            $table->integer("interest_shelve")->index();
            $table->integer("term_profit_loss")->index();
            $table->date("at_first_loaned_on")->nullable();
            $table->date("last_term_on")->nullable();
            $table->date("old_last_term_on")->nullable();
            $table->date("last_traded_on")->nullable();
            $table->double("at_first_loan_amount");
            $table->double("loan_balance");
            $table->double("ryuho_balance");
            $table->double("ryuho_interest");
            $table->float("contract_interest_rate");
            $table->float("delay_interest_rate");
            $table->integer("lir_number")->index();
            $table->integer("lar_number")->index();
            $table->integer("cir_number")->index();
            $table->integer("ringi_number");
            $table->char("customer_name", 30);
            $table->date("contract_term_on")->nullable();
            $table->date("damage_started_on")->nullable();
            $table->double("contract_principal");
            $table->double("contract_interest");
            $table->double("uncollected_interest");
            $table->double("delay_contract_principal");
            $table->double("delay_contract_interest");
            $table->integer("delay_date_count");
            $table->double("damage_principal_cumulation");
            $table->double("damage_interest_cumulation");
            $table->double("receivable_damage_contract_interest_rate");
            $table->double("receivable_damage_delay_interest_rate");
            $table->integer("delay_count");
            $table->integer("part_collection_state");
            $table->double("part_collection_principal");
            $table->double("part_collection_interest");
            $table->char("previous_prefecture_code", 1);
            $table->char("spare_1", 25);
            $table->char("spare_2", 114);
            $table->integer("monthly_id")->index();
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
