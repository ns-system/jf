<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositManagementLedgers extends Migration
{

    public $tableName = 'deposit_management_ledgers';
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
            $table->integer("subject_code")->index();
            $table->integer("settlement_item_state")->index();
            $table->integer("interest_rate_state")->index();
            $table->integer("base_interest_rate_state")->index();
            $table->integer("type_code")->index();
            $table->integer("category_code")->index();
            $table->integer("product_code")->index();
            $table->integer("assist_product_code")->index();
            $table->integer("interest_rate_application_state")->index();
            $table->integer("term_code")->index();
            $table->integer("special_control_code")->index();
            $table->double("month_end_balance");
            $table->double("unpaid_interest");
            $table->double("unpaid_deposit_interest");
            $table->double("unpaid_profit_compensation");
            $table->double("unpaid_prepaid_discount_fee");
            $table->double("collected_delay_interest");
            $table->integer("month_end_account_count");
            $table->double("monthly_cumulation");
            $table->double("monthly_debit_amount");
            $table->double("monthly_credit_amount");
            $table->double("monthly_payment_interest");
            $table->double("monthly_payment_deposit_interest");
            $table->double("monthly_payment_compensation");
            $table->double("monthly_payment_prepaid_discount_fee");
            $table->double("monthly_collection_delay_interest");
            $table->integer("monthly_deposit_accept_count");
            $table->integer("monthly_deposit_payment_count");
            $table->double("this_year_cumulation_balance");
            $table->double("last_year_cumulation_balance");
            $table->double("yearly_debit_amount");
            $table->double("yearly_credit_amount");
            $table->double("term_payment_interest");
            $table->double("term_deposit_payment_interest");
            $table->double("term_payment_compensation");
            $table->double("term_payment_prepaid_discount_fee");
            $table->double("term_collection_delay_interest");
            $table->integer("term_accept_deposit_count");
            $table->integer("term_deposit_payment_count");
            $table->double("at_first_contract_amount");
            $table->double("interest_rate_balance_cumulation");
            $table->double("new_interest_rate_balance_cumulation");
            $table->double("cancellation_interest_rate_balance_cumulation");
            $table->double("accept_term_balance_cumulation");
            $table->date("month_end_renewed_on")->nullable();
            $table->char("previous_prefecture_code", 1);
            $table->char("spare_1", 9);
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
