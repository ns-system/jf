<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanTermLedgers extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'loan_term_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("data_state");
            $table->integer("file_state");
            $table->integer("prefecture_code");
            $table->integer("organization_code");
            $table->integer("store_number");
            $table->double("account_number");
            $table->integer("contract_number");
            $table->char("spare_1", 14);
            $table->date("created_base_on");
            $table->integer("return_prefecture_code");
            $table->integer("second_organization_code");
            $table->integer("second_store_number")->index();
            $table->integer("small_store_number")->index();
            $table->date("term_on");
            $table->integer("subject_code")->index();
            $table->double("deposit_amount")->index();
            $table->double("payment_amount")->index();
            $table->char("process_detail", 10);
            $table->double("second_account_number");
            $table->integer("deposit_number");
            $table->integer("fund_code");
            $table->integer("fund_state");
            $table->integer("assist_fund_code");
            $table->integer("fund_usage_1");
            $table->integer("fund_usage_2");
            $table->integer("customer_number");
            $table->integer("lir_number");
            $table->char("management_number", 15);
            $table->date("executed_on");
            $table->date("last_term_on");
            $table->double("loan_amount");
            $table->double("loan_balance");
            $table->float("contract_interest_rate");
            $table->char("execution_state", 20);
            $table->integer("scheduled_count");
            $table->double("redemption_principal");
            $table->double("interest_amount");
            $table->double("security_fee");
            $table->double("redemption_amount");
            $table->double("general_redemption_principal");
            $table->double("general_interest");
            $table->double("general_redemption_amount");
            $table->double("bonus_redemption_principal");
            $table->double("bonus_interest");
            $table->double("bonus_redemption_amount");
            $table->double("after_redemption_balance");
            $table->date("second_created_base_on");
            $table->integer("created_base_month");
            $table->integer("head_count");
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
