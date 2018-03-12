<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanTradingFiles extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'loan_trading_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("record_state")->index();
            $table->integer("subject_code")->index();
            $table->integer("trade_state")->index();
            $table->integer("handle_state")->index();
            $table->integer("correction_state")->index();
            $table->integer("denomination")->index();
            $table->integer("handle_prefecture_code")->index();
            $table->integer("handle_organization_code")->index();
            $table->integer("handle_store_number")->index();
            $table->integer("original_prefecture_code")->index();
            $table->integer("original_organization_code")->index();
            $table->integer("original_store_number")->index();
            $table->integer("activation_state")->index();
            $table->integer("error_state")->index();
            $table->integer("officer_usage_reason_code")->index();
            $table->char("kanji_name", 30);
            $table->integer("parent_cir_number")->index();
            $table->integer("conversion_state")->index();
            $table->integer("system_consecutive_number")->index();
            $table->integer("closed_state")->index();
            $table->integer("finance_state")->index();
            $table->integer("consignor_code")->index();
            $table->integer("jifuri_impossible_reason_state")->index();
            $table->integer("message_number");
            $table->date("handled_on")->nullable();
            $table->integer("parent_lir_number")->index();
            $table->double("loan_account_number")->index();
            $table->integer("collection_advance_state");
            $table->integer("payment_state")->index();
            $table->integer("pay_off_state")->index();
            $table->double("principal");
            $table->double("increase_amount_principal");
            $table->float("contract_interest_rate");
            $table->double("contract_interest_amount");
            $table->double("increase_contract_interest_amount");
            $table->float("pay_back_interest_rate");
            $table->double("pay_back_interest_amount");
            $table->double("increase_pay_back_interest_amount");
            $table->float("delay_interest_rate");
            $table->double("delay_interest_amount");
            $table->double("increase_delay_interest_amount");
            $table->double("stamp_fee");
            $table->double("fee");
            $table->double("other_deduction_amount");
            $table->double("trade_amount");
            $table->double("juko_tegashi_trade_amount");
            $table->double("ryuho_interest");
            $table->double("ryuho_amount_balance");
            $table->double("ukeire_interest");
            $table->double("ukeire_amount_balance");
            $table->double("reception_number")->index();
            $table->integer("jifuri_process_state")->index();
            $table->integer("jifuri_state")->index();
            $table->integer("transfer_payment_store_number")->index();
            $table->integer("transfer_payment_subject")->index();
            $table->double("transfer_payment_account_number")->index();
            $table->integer("interlock_finance_state")->index();
            $table->double("transfer_payment_amount");
            $table->integer("transfer_payment_code")->index();
            $table->integer("proxy_institution_code")->index();
            $table->integer("fund_state")->index();
            $table->integer("gist_code")->index();
            $table->integer("lir_store_number")->index();
            $table->integer("lir_fishery_style")->index();
            $table->integer("loan_category");
            $table->integer("htr_state");
            $table->integer("htr_state_01");
            $table->integer("htr_state_02");
            $table->integer("htr_state_03");
            $table->integer("htr_state_04");
            $table->integer("htr_state_05");
            $table->integer("htr_state_06");
            $table->integer("htr_state_07");
            $table->integer("htr_state_08");
            $table->integer("htr_state_09");
            $table->integer("htr_state_10");
            $table->integer("htr_state_11");
            $table->integer("htr_state_12");
            $table->integer("htr_state_13");
            $table->integer("htr_state_14");
            $table->integer("htr_state_15");
            $table->integer("htr_state_16");
            $table->integer("htr_state_17");
            $table->integer("htr_state_18");
            $table->integer("htr_state_19");
            $table->integer("htr_state_20");
            $table->integer("htr_state_21");
            $table->integer("htr_state_22");
            $table->integer("htr_state_23");
            $table->integer("htr_state_24");
            $table->integer("htr_state_25");
            $table->integer("htr_state_26");
            $table->integer("htr_state_27");
            $table->integer("htr_state_28");
            $table->integer("htr_state_29");
            $table->integer("htr_state_30");
            $table->integer("htr_state_31");
            $table->integer("htr_state_32");
            $table->integer("htr_state_33");
            $table->integer("htr_state_34");
            $table->integer("htr_state_35");
            $table->integer("htr_state_36");
            $table->integer("htr_state_37");
            $table->integer("htr_state_38");
            $table->integer("htr_state_39");
            $table->integer("htr_state_40");
            $table->integer("htr_state_41");
            $table->integer("htr_state_42");
            $table->integer("htr_state_43");
            $table->integer("htr_state_44");
            $table->integer("htr_state_45");
            $table->integer("htr_state_46");
            $table->integer("htr_state_47");
            $table->integer("htr_state_48");
            $table->integer("htr_state_49");
            $table->integer("htr_state_50");
            $table->integer("htr_state_51");
            $table->integer("htr_state_52");
            $table->integer("htr_state_53");
            $table->integer("htr_state_54");
            $table->integer("htr_state_55");
            $table->integer("htr_state_56");
            $table->integer("principal_payment_state")->index();
            $table->integer("interest_payment_state")->index();
            $table->integer("deduction_amount_payment_state")->index();
            $table->integer("pay_back_payment_state")->index();
            $table->integer("pay_back_security_fee");
            $table->double("uncollected_contract_interest");
            $table->double("uncollected_delay_interest");
            $table->double("uncollected_security_fee");
            $table->integer("accept_security_fee");
            $table->integer("accept_fee");
            $table->integer("repayment_count");
            $table->integer("advance_count");
            $table->double("security_trade_amount");
            $table->integer("paper_fee");
            $table->integer("deduction_amount");
            $table->integer("renewal_state");
            $table->double("retention_accept");
            $table->integer("date_count");
            $table->double("fixed_interest_target_cumulation");
            $table->date("delay_damage_handled_on")->nullable();
            $table->double("collection_security_fee");
            $table->double("delay_damage_security_fee");
            $table->integer("assist_collection_advance_state");
            $table->double("reduction_security_fee");
            $table->double("reduction_delay_security_fee");
            $table->double("reduction_delay_interest_amount");
            $table->double("contract_security_fee");
            $table->double("delay_security_fee");
            $table->double("security_collection_fee");
            $table->double("receivable_delay_interest");
            $table->double("receivable_delay_security_fee");
            $table->date("delay_security_fee_handled_on")->nullable();
            $table->double("rebuild_contract_repayment_principal");
            $table->double("rebuild_collection_principal");
            $table->double("rebuild_delay_principal");
            $table->double("rebuild_increase_contract_repayment_principal");
            $table->double("rebuild_increase_collection_principal");
            $table->double("rebuild_increase_delay_principal");
            $table->double("rebuild_receivable_delay_interest");
            $table->date("rebuild_delay_damage_handled_on")->nullable();
            $table->double("rebuild_receivable_delay_security_fee");
            $table->date("rebuild_delay_security_fee_handled_on")->nullable();
            $table->char("spare_1", 23);
            $table->integer("jrt_state_1_01");
            $table->integer("jrt_state_1_02");
            $table->integer("jrt_state_1_03");
            $table->integer("jrt_state_1_04");
            $table->integer("jrt_state_1_05");
            $table->integer("jrt_state_1_06");
            $table->integer("jrt_state_1_07");
            $table->integer("jrt_state_1_08");
            $table->integer("jrt_state_1_09");
            $table->integer("jrt_state_1_10");
            $table->integer("jrt_state_1_11");
            $table->integer("jrt_state_1_12");
            $table->integer("jrt_state_1_13");
            $table->integer("jrt_state_1_14");
            $table->integer("jrt_state_1_15");
            $table->integer("jrt_state_1_16");
            $table->integer("jrt_state_2_01");
            $table->integer("jrt_state_2_02");
            $table->integer("jrt_state_2_03");
            $table->integer("jrt_state_2_04");
            $table->integer("jrt_state_2_05");
            $table->integer("jrt_state_2_06");
            $table->integer("jrt_state_2_07");
            $table->integer("jrt_state_2_08");
            $table->integer("jrt_state_2_09");
            $table->integer("jrt_state_2_10");
            $table->integer("jrt_state_2_11");
            $table->integer("jrt_state_2_12");
            $table->integer("jrt_state_2_13");
            $table->integer("jrt_state_2_14");
            $table->integer("jrt_state_2_15");
            $table->integer("jrt_state_2_16");
            $table->integer("jrt_state_2_17");
            $table->integer("jrt_state_2_18");
            $table->integer("jrt_state_2_19");
            $table->integer("jrt_state_2_20");
            $table->integer("jrt_state_2_21");
            $table->integer("jrt_state_2_22");
            $table->integer("jrt_state_2_23");
            $table->integer("jrt_state_2_24");
            $table->integer("application_interest_rate");
            $table->integer("proxy_receive_code");
            $table->integer("proxy_receive_store_number");
            $table->integer("proxy_receive_subject");
            $table->double("proxy_receive_account_number");
            $table->integer("uncollected_bill_interest");
            $table->integer("security_fee");
            $table->integer("register_fee");
            $table->integer("danshin_special_fee");
            $table->integer("fire_insurance_fee");
            $table->integer("earthquake_insurance_fee");
            $table->date("bill_issued_on")->nullable();
            $table->integer("juko_store_code")->index();
            $table->char("credit_number", 15)->index();
            $table->double("juko_account_number")->index();
            $table->integer("juko_loan_type")->index();
            $table->integer("major_item_category")->index();
            $table->integer("land_fee");
            $table->integer("yucho_ addition_amount");
            $table->integer("tr_number");
            $table->date("reckoned_on")->nullable();
            $table->integer("operator_code")->index();
            $table->integer("officer_code")->index();
            $table->date("traded_on")->nullable();
            $table->integer("operation_time");
            $table->date("daily_account_paid_on")->nullable();
            $table->integer("terminal_type");
            $table->integer("small_store_number")->index();
            $table->double("reduction_interest_amount");
            $table->integer("contract_fishery_store_number")->index();
            $table->integer("commencement_number")->index();
            $table->char("journalizing_number")->index();
            $table->integer("account_management_store_number");
            $table->integer("previous_prefecture_code");
            $table->string("spare_2");
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