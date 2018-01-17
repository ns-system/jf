<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositTradingFiles extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'deposit_trading_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("record_code")->index();
            $table->integer("handle_prefecture_code")->index();
            $table->integer("handle_organization_code")->index();
            $table->integer("handle_store_number")->index();
            $table->integer("original_prefecture_code")->index();
            $table->integer("original_organization_code")->index();
            $table->integer("original_store_number")->index();
            $table->integer("subject_code")->index();
            $table->integer("trade_state")->index();
            $table->integer("handle_state")->index();
            $table->integer("correction_state")->index();
            $table->integer("denomination")->index();
            $table->integer("activation_state")->index();
            $table->integer("error_state")->index();
            $table->integer("officer_usage_reason_code")->index();
            $table->integer("customer_number")->index();
            $table->char("kana_name", 30);
            $table->integer("closed_state")->index();
            $table->integer("finance_state")->index();
            $table->integer("consignor_code")->index();
            $table->integer("jifuri_impossible_reason_state")->index();
            $table->integer("message_number");
            $table->integer("transfer_to_subject_code")->index();
            $table->integer("transfer_to_store_number")->index();
            $table->double("transfer_to_account_number")->index();
            $table->integer("term_code")->index();
            $table->integer("special_notification_state");
            $table->date("last_traded_on")->nullable();
            $table->integer("denomination_detail");
            $table->double("deposit_interest");
            $table->double("contract_interest");
            $table->double("overdraft_interest");
            $table->double("security_fee");
            $table->double("delay_damage_security_interest");
            $table->double("profit_compensation");
            $table->double("prepaid_discount_fee");
            $table->double("delay_interest");
            $table->integer("taxation_code_1");
            $table->integer("taxation_code_2");
            $table->integer("taxation_code_3");
            $table->integer("old_national_tax");
            $table->integer("national_tax");
            $table->integer("local_tax");
            $table->integer("bill_check_state")->index();
            $table->integer("bill_check_number")->index();
            $table->double("overdraft_float_amount");
            $table->double("profit_contract_amount");
            $table->double("new_principal");
            $table->double("trade_amount");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->float("interest_rate");
            $table->date("deposited_on")->nullable();
            $table->float("midterm_cancellation_interest_rate");
            $table->date("maturity_on")->nullable();
            $table->double("surface_balance");
            $table->double("other_bank_ticket_amount");
            $table->integer("office_code");
            $table->char("employee_number", 12);
            $table->date("previous_interest_settlement_on")->nullable();
            $table->integer("category_code")->index();
            $table->integer("bankbook_state")->index();
            $table->integer("handle_type")->index();
            $table->integer("product_code")->index();
            $table->integer("bankbook_deed_state")->index();
            $table->integer("bankbook_deed_code")->index();
            $table->integer("bankbook_deed_type")->index();
            $table->integer("assist_product_code")->index();
            $table->integer("overdraft_product_code")->index();
            $table->integer("execution_state")->index();
            $table->integer("tsumitate_state")->index();
            $table->integer("adjustment_code")->index();
            $table->integer("auto_continuous_state")->index();
            $table->integer("interest_payment_state")->index();
            $table->integer("new_maruyu_state")->index();
            $table->double("child_teiki_principal");
            $table->integer("child_teiki_interest");
            $table->integer("child_teiki_national_tax");
            $table->integer("child_teiki_local_tax");
            $table->date("maruyu_taxation_on")->nullable();
            $table->double("separation_taxation_target_interest");
            $table->date("reckoned_on")->nullable();
            $table->integer("operator_code")->index();
            $table->integer("officer_code")->index();
            $table->char("comment", 20);
            $table->date("traded_on")->nullable();
            $table->integer("operation_time");
            $table->date("daily_account_paid_on")->nullable();
            $table->integer("terminal_type")->index();
            $table->integer("continuous_count");
            $table->integer("zaikei_deposit_state");
            $table->char("before_connection_product_code", 2);
            $table->integer("small_store_number")->index();
            $table->char("gist_kanji", 20);
            $table->integer("commencement_number")->index();
            $table->char("journalizing_number", 6)->index();
            $table->integer("account_management_store_number");
            $table->integer("authentication_state")->index();
            $table->char("previous_prefecture_code", 1);
            $table->integer("movable_terminal_assist_store_code")->index();
            $table->integer("movable_terminal_number")->index();
            $table->char("movable_terminal_serial_number", 2)->index();
            $table->integer("qualification_code")->index();
            $table->integer("personality_code")->index();
            $table->integer("voucher_state")->index();
            $table->string("spare");
            $table->double("key_account_number")->index();
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
