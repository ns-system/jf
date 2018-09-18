<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWariteLoanLedgers extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'warite_loan_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("common_id")->unsigned()->index();
            $table->double("loan_account_number")->index();
            $table->double("collection_amount");
            $table->date("last_bill_term_on")->nullable();
            $table->integer("application_count");
            $table->integer("execution_count");
            $table->integer("collection_count");
            $table->integer("dishonor_count");
            $table->integer("delay_account_count");
            $table->integer("detail_register_count");
            $table->double("detail_register_amount");
            $table->integer("discount_rate");
            $table->date("payment_executed_on")->nullable();
            $table->integer("principal_payment_state")->index();
            $table->integer("interest_payment_state")->index();
            $table->integer("deduction_amount_payment_state")->index();
            $table->integer("discount_fee_calculate_state")->index();
            $table->integer("discount_fee_deposit_state")->index();
            $table->date("discount_fee_traded_1_on")->nullable();
            $table->date("discount_fee_traded_2_on")->nullable();
            $table->date("discount_fee_traded_3_on")->nullable();
            $table->double("discount_fee_amount");
            $table->integer("collection_fee_jifuri_state")->index();
            $table->integer("collection_fee_deposit_state")->index();
            $table->date("collection_fee_traded_on")->nullable();
            $table->date("collection_fee_handled_on")->nullable();
            $table->date("collection_fee_payment_on")->nullable();
            $table->integer("collection_fee_amount");
            $table->integer("fee");
            $table->integer("stamp_fee");
            $table->double("other_deduction_amount");
            $table->date("interest_rate_modify_on")->nullable();
            $table->float("delay_interest_rate");
            $table->date("interest_term_on")->nullable();
            $table->date("principal_term_on")->nullable();
            $table->double("uncollected_discount_fee");
            $table->double("uncollected_delay_discount_fee");
            $table->double("past_term_balance");
            $table->double("past_month_balance");
            $table->date("average_balance_base_on")->nullable();
            $table->char("security_deed_number", 20);
            $table->string("spare_1");
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
