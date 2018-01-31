<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanAnalysisLedgers extends Migration
{

    public $tableName = 'loan_analysis_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("create_month");
            $table->integer("prefecture_code")->index();
            $table->integer("control_store_number")->index();
            $table->integer("organization_code")->index();
            $table->integer("store_number")->index();
            $table->integer("store_state")->index();
            $table->integer("customer_fishery_store_number")->index();
            $table->integer("small_store_number")->index();
            $table->integer("area_code")->index();
            $table->integer("qualification_code")->index();
            $table->integer("personality_code")->index();
            $table->integer("fishery_style")->index();
            $table->integer("gender")->index();
            $table->integer("subject_code")->index();
            $table->integer("settlement_item_state")->index();
            $table->integer("interest_rate_state")->index();
            $table->integer("base_interest_rate_state")->index();
            $table->integer("capital_fund_state")->index();
            $table->integer("case_state")->index();
            $table->integer("loan_category")->index();
            $table->integer("fund_state")->index();
            $table->integer("assist_fund_code")->index();
            $table->integer("interest_supply_state")->index();
            $table->integer("fund_usage_2")->index();
            $table->integer("fund_usage_1")->index();
            $table->integer("society_system_state")->index();
            $table->integer("special_control_code")->index();
            $table->double("month_end_balance");
            $table->double("uncollected_interest");
            $table->double("general_uncollected_interest");
            $table->double("previous_accept_interest");
            $table->double("unrequired_interest");
            $table->double("uncollected_delay_interest_interest");
            $table->double("delay_contract_interest");
            $table->double("receivable_interest_supply");
            $table->double("delay_contract_principal");
            $table->double("delay_principal_cumulation");
            $table->double("contract_delay_uncollected_interest");
            $table->double("base_delay_uncollected_interest");
            $table->double("damage_delay_uncollected_interest");
            $table->integer("month_end_loan_account_count");
            $table->double("monthly_loan_amount");
            $table->double("monthly_contract_redemption");
            $table->double("monthly_advance_redemption");
            $table->double("monthly_delay_deposit");
            $table->double("monthly_renewal_amount");
            $table->double("monthly_balance_cumulation");
            $table->double("monthly_contract_cumulation");
            $table->double("monthly_debit_amount");
            $table->double("monthly_credit_amount");
            $table->double("monthly_collection_interest");
            $table->double("monthly_collection_contract_interest");
            $table->double("monthly_collection_delay_damage");
            $table->double("monthly_interest_supply_assist");
            $table->integer("monthly_loan_execution_count");
            $table->integer("monthly_loan_pay_off_count");
            $table->double("term_loan_amount");
            $table->double("term_contract_redemption");
            $table->double("term_advance_redemption");
            $table->double("term_delay_deposit");
            $table->double("term_renewal_amount");
            $table->double("term_balance_cumulation");
            $table->double("term_contract_cumulation");
            $table->double("term_debit_amount");
            $table->double("term_credit_amount");
            $table->double("term_collection_interest");
            $table->double("term_collection_contract_interest");
            $table->double("term_collection_delay_damage");
            $table->double("term_interest_supply_assist");
            $table->integer("term_loan_execution_count");
            $table->integer("term_loan_pay_off_count");
            $table->date("month_end_renewed_on")->nullable();
            $table->double("other_assist_institution");
            $table->char("previous_prefecture_code", 1);
            $table->char("spare_3", 2);
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
