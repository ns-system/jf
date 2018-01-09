<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShogashiLoanLedgers extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'shogashi_loan_ledgers';
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
            $table->integer("delay_interest_state")->index();
            $table->integer("allotment_adjustment")->index();
            $table->integer("last_allotment_adjustment")->index();
            $table->integer("increase_allotment_calculate_state")->index();
            $table->integer("contract_month_interest_rate_length");
            $table->integer("contract_date_interest_rate_length");
            $table->integer("delay_date_interest_rate_length");
            $table->integer("allotment_interest_rate_length");
            $table->integer("cycle_rate_length");
            $table->integer("first_interest_length");
            $table->double("increase_loan_amount");
            $table->double("increase_loan_balance");
            $table->integer("equal_repayment_cycle");
            $table->double("equal_repayment_amount");
            $table->date("first_equal_repaid_on")->nullable();
            $table->integer("increase_repayment_cycle");
            $table->double("increase_repayment_amount");
            $table->date("first_increase_repayment_on")->nullable();
            $table->date("last_increase_repaid_on")->nullable();
            $table->integer("interest_repayment_cycle");
            $table->date("first_interest_repaid_on")->nullable();
            $table->integer("contract_repay_date");
            $table->date("next_contract_repaid_on")->nullable();
            $table->date("next_principal_repaid_on")->nullable();
            $table->date("next_interest_repaid_on")->nullable();
            $table->integer("repayment_total_count");
            $table->integer("residue_repayment_count");
            $table->date("principal_term_on")->nullable();
            $table->date("interest_term_on")->nullable();
            $table->date("increase_interest_term_on")->nullable();
            $table->double("uncollected_contract_interest");
            $table->double("uncollected_delay_interest");
            $table->date("long_term_prime_rate_base_on")->nullable();
            $table->integer("spread");
            $table->float("contract_interest_rate");
            $table->float("delay_interest_rate");
            $table->integer("interest_grant_unit");
            $table->integer("interest_rate_modify_state_1")->index();
            $table->date("interest_rate_modify_1_on")->nullable();
            $table->float("modify_interest_rate_1");
            $table->integer("interest_rate_modify_state_2")->index();
            $table->date("interest_rate_modify_2_on")->nullable();
            $table->float("modify_interest_rate_2");
            $table->integer("interest_rate_modify_state_3")->index();
            $table->date("interest_rate_modify_3_on")->nullable();
            $table->float("modify_interest_rate_3");
            $table->double("past_term_balance");
            $table->double("past_month_balance");
            $table->date("average_balance_base_on")->nullable();
            $table->double("first_repayment_amount");
            $table->double("last_repayment_amount");
            $table->integer("security_fee");
            $table->integer("fee");
            $table->integer("stamp_fee");
            $table->double("etcetera_deduction_amount");
            $table->char("security_deed_number", 20);
            $table->date("step_term_on")->nullable();
            $table->integer("increase_uncollected_interest");
            $table->integer("fee_rate");
            $table->integer("security_fee_rate");
            $table->date("repayment_detail_issued_on")->nullable();
            $table->date("float_interest_rate_modified_on")->nullable();
            $table->integer("contract_fishery_store_number")->index();
            $table->double("add_on_necessary_fund");
            $table->integer("add_on_security_fee");
            $table->integer("yearly_list_output_cycle");
            $table->integer("limit_revolving_collateral_state")->index();
            $table->integer("conservation_number")->index();
            $table->integer("third_party_collateral_lir_number");
            $table->float("interest_rate");
            $table->double("adjustment_every_repayment_amount");
            $table->double("adjustment_increase_repayment_amount");
            $table->integer("previous_delay_detail_count");
            $table->integer("security_institution_code")->index();
            $table->integer("security_fund_code")->index();
            $table->integer("base_border_state")->index();
            $table->integer("security_calculation_condition")->index();
            $table->integer("security_fee_collection_state")->index();
            $table->date("next_security_fee_repaid_on")->nullable();
            $table->date("security_fee_term_on")->nullable();
            $table->float("delay_damage_security_interest_rate");
            $table->integer("permission_state_01");
            $table->integer("permission_state_02");
            $table->integer("permission_state_03");
            $table->integer("permission_state_04");
            $table->integer("permission_state_05");
            $table->integer("permission_state_06");
            $table->integer("permission_state_07");
            $table->integer("permission_state_08");
            $table->integer("permission_state_09");
            $table->integer("permission_state_10");
            $table->integer("permission_state_11");
            $table->integer("permission_state_12");
            $table->integer("permission_state_13");
            $table->integer("permission_state_14");
            $table->integer("permission_state_15");
            $table->integer("permission_state_16");
            $table->date("permission_extended_on")->nullable();
            $table->date("spread_modify_1_on")->nullable();
            $table->float("spread_modify_interest_rate_1");
            $table->date("spread_modify_2_on")->nullable();
            $table->float("spread_modify_interest_rate_2");
            $table->char("spare_1", 166);
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
