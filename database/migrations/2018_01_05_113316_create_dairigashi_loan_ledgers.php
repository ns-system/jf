<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDairigashiLoanLedgers extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'dairigashi_loan_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("common_id")->unsigned()->index();
            $table->double("loan_account_number")->index();
            $table->integer("repayment_category")->index();
            $table->integer("interest_payment_type")->index();
            $table->integer("fraction_adjustment_state")->index();
            $table->integer("interest_calculation_state")->index();
            $table->integer("delay_interest_calculation_type")->index();
            $table->integer("interest_calculation_base_code")->index();
            $table->integer("first_interest_calculation_state")->index();
            $table->integer("last_interest_calculation_state")->index();
            $table->integer("daily_interest_calculation_type")->index();
            $table->integer("monthly_interest_calculation_type")->index();
            $table->integer("holiday_repayment_state")->index();
            $table->integer("division_execution_state")->index();
            $table->integer("step_state")->index();
            $table->integer("phase_interest_rate_state")->index();
            $table->integer("calculation_state")->index();
            $table->integer("prepaid_interest_state")->index();
            $table->integer("advance_repayment_interest_state")->index();
            $table->integer("delay_interest_calculation_state")->index();
            $table->integer("delay_interest_target_state")->index();
            $table->integer("allotment_adjustment")->index();
            $table->integer("last_allotment_adjustment")->index();
            $table->integer("increase_allotment_calculation_state");
            $table->integer("contract_month_interest_rate_length");
            $table->integer("contract_date_interest_rate_length");
            $table->integer("delay_date_interest_rate_length");
            $table->integer("allotment_interest_rate_length");
            $table->integer("cycle_rate_length");
            $table->integer("first_interest_length");
            $table->double("loan_increase_amount");
            $table->double("increase_loan_balance");
            $table->integer("equal_repayment_cycle");
            $table->double("equal_repayment_amount");
            $table->date("first_equal_repaid_on")->nullable();
            $table->integer("increase_repayment_cycle");
            $table->double("increase_repayment_amount");
            $table->date("first_increase_repaid_on")->nullable();
            $table->date("last_increase_repaid_on")->nullable();
            $table->integer("interest_repayment_cycle");
            $table->date("first_interest_repaid_on")->nullable();
            $table->integer("contract_repay_date");
            $table->date("next_contract_repaid_on")->nullable();
            $table->date("next_principal_repaid_on")->nullable();
            $table->date("next_interest_repaid_on")->nullable();
            $table->integer("repayment_count");
            $table->integer("residue_repayment_count");
            $table->date("principal_term_on")->nullable();
            $table->date("interest_term_on")->nullable();
            $table->date("increase_interest_term_on")->nullable();
            $table->double("uncollected_contract_interest");
            $table->double("uncollected_delay_interest");
            $table->date("long_term_prime_rate_base_on")->nullable();
            $table->Integer("spread");
            $table->float("contract_interest_rate");
            $table->float("delay_interest_rate");
            $table->integer("interest_grant_unit");
            $table->integer("interest_rate_modify_1_state")->index();
            $table->date("interest_rate_modified_1_on")->nullable();
            $table->float("modify_interest_rate_1");
            $table->integer("interest_rate_modify_2_state")->index();
            $table->date("interest_rate_modified_2_on")->nullable();
            $table->float("modify_interest_rate_2");
            $table->integer("interest_rate_modify_3_state")->index();
            $table->date("interest_rate_modified_3_on")->nullable();
            $table->float("modify_interest_rate_3");
            $table->double("past_term_balance");
            $table->double("past_month_balance");
            $table->date("average_balance_base_on")->nullable();
            $table->double("first_repayment_amount");
            $table->double("last_repayment_amount");
            $table->Integer("security_fee");
            $table->Integer("fee");
            $table->Integer("stamp_fee");
            $table->double("other_deduction_amount");
            $table->char("security_deed_number", 20);
            $table->date("step_term")->nullable();
            $table->Integer("increase_uncollected_interest");
            $table->Integer("fee_rate");
            $table->Integer("security_fee_rate");
            $table->date("repayment_detail_issued_on")->nullable();
            $table->date("float_interest_rate_modified_on")->nullable();
            $table->integer("contract_fishery_store_number")->index();
            $table->float("security_rate");
            $table->integer("fee_calculation_state")->index();
            $table->char("proxy_trade_number", 16);
            $table->char("local_year", 1);
            $table->char("noko_decide_number", 12);
            $table->integer("juko_awasegashi_state")->index();
            $table->char("credit_number", 15);
            $table->integer("juko_credit_category")->index();
            $table->double("reception_number")->index();
            $table->integer("juko_store_code")->index();
            $table->integer("major_item_category")->index();
            $table->integer("loan_type_code")->index();
            $table->integer("construction_site_code")->index();
            $table->integer("juko_reception_count_code")->index();
            $table->integer("accident_code")->index();
            $table->integer("application_fiscal_year");
            $table->integer("juko_monthly_report_state")->index();
            $table->Integer("security_deed_number_2");
            $table->float("float_base_interest_rate");
            $table->integer("juko_exception_item")->index();
            $table->date("fund_granted_on")->nullable();
            $table->double("fund_grant_amount");
            $table->integer("juko_conservation_state_01");
            $table->integer("juko_conservation_state_02");
            $table->integer("juko_conservation_state_03");
            $table->integer("juko_conservation_state_04");
            $table->integer("juko_conservation_state_05");
            $table->integer("juko_conservation_state_06");
            $table->integer("juko_conservation_state_07");
            $table->integer("juko_conservation_state_08");
            $table->integer("juko_conservation_state_09");
            $table->integer("juko_conservation_state_10");
            $table->integer("juko_conservation_state_11");
            $table->integer("juko_conservation_state_12");
            $table->integer("juko_conservation_state_13");
            $table->integer("juko_conservation_state_14");
            $table->integer("juko_conservation_state_15");
            $table->integer("juko_conservation_state_16");
            $table->integer("juko_credit_state")->index();
            $table->integer("zaikei_interest_supply_state")->index();
            $table->double("juko_customer_number")->index();
            $table->date("kinsho_contracted_on")->nullable();
            $table->integer("filioparental_relay_state")->index();
            $table->integer("limit_revolving_collateral_state")->index();
            $table->integer("conservation_number")->index();
            $table->Integer("third_party_collateral_lir_number")->index();
            $table->float("base_interest_rate");
            $table->double("adjustment_every_repayment_amount");
            $table->double("adjustment_increase_repayment_amount");
            $table->integer("previous_delay_detail_count");
            $table->char("spare", 113);
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
