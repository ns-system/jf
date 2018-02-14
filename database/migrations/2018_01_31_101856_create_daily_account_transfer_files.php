<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyAccountTransferFiles extends Migration
{

    public $tableName = 'daily_account_transfer_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("record_code")->index();
            $table->integer("prefecture_code")->index();
            $table->integer("store_number")->index();
            $table->integer("organization_code")->index();
            $table->integer("expense_subject_code")->index();
            $table->integer("expense_state")->index();
            $table->integer("spare_1")->index();
            $table->integer("is_calculation_invert")->index();
            $table->integer("is_online_major_subject")->index();
            $table->integer("is_online_subject")->index();
            $table->integer("is_exist_expense_detail")->index();
            $table->integer("is_head_office_pay_subject")->index();
            $table->integer("is_total_record")->index();
            $table->integer("is_statistics_record")->index();
            $table->integer("is_permit_minus")->index();
            $table->integer("spare_2")->index();
            $table->integer("is_deposit_and_payment")->index();
            $table->integer("spare_3")->index();
            $table->integer("is_control_other_store")->index();
            $table->integer("is_total_payment_ledger")->index();
            $table->integer("is_total_trial_balance")->index();
            $table->integer("is_register_expense_detail")->index();
            $table->integer("is_unique_fishery_subject")->index();
            $table->integer("is_center_control_expense")->index();
            $table->integer("spare_4")->index();
            $table->integer("spare_5")->index();
            $table->integer("spare_6")->index();
            $table->integer("spare_7")->index();
            $table->integer("spare_8")->index();
            $table->integer("spare_9")->index();
            $table->integer("trade_state")->index();
            $table->integer("execution_state")->index();
            $table->integer("general_state")->index();
            $table->integer("denomination_state")->index();
            $table->double("debit_amount");
            $table->double("credit_amount");
            $table->integer("detail_code")->index();
            $table->char("gist_half_kana", 20);
            $table->char("gist_kanji", 20);
            $table->integer("correction_state")->index();
            $table->integer("settlement_modify_state")->index();
            $table->integer("consumption_tax_state")->index();
            $table->integer("social_expense_state")->index();
            $table->integer("transfer_to_subject_code")->index();
            $table->double("transfer_to_account_number")->index();
            $table->date("traded_on")->nullable();
            $table->integer("operation_time");
            $table->date("daily_account_paid_on")->nullable();
            $table->date("reckoned_on")->nullable();
            $table->integer("input_store_number")->index();
            $table->integer("small_store_number")->index();
            $table->integer("debit_count");
            $table->integer("credit_count");
            $table->integer("commencement_number")->index();
            $table->char("journalizing_number", 6)->index();
            $table->integer("operator_code")->index();
            $table->integer("officer_code")->index();
            $table->char("spare_10", 8);
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
