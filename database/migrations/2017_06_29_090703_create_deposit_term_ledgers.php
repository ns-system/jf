<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositTermLedgers extends Migration
{

    public $tableName = 'deposit_term_ledgers';
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
            $table->date("created_base_on")->nullable()->default('0000-00-00');
            $table->integer("return_prefecture_code");
            $table->integer("second_organization_code");
            $table->integer("second_store_number")->index();
            $table->integer("small_store_number")->index();
            $table->date("term_on")->nullable()->default('0000-00-00');
            $table->integer("subject_code")->index();
            $table->double("second_account_number")->index();
            $table->integer("deposit_number")->index();
            $table->double("deposit_amount");
            $table->double("payment_amount");
            $table->char("process_detail", 10);
            $table->integer("category_code");
            $table->integer("assist_product_code");
            $table->integer("customer_number");
            $table->integer("filioparental_state");
            $table->integer("taxation_code");
            $table->integer("interest_handle");
            $table->date("contracted_on")->nullable()->default('0000-00-00');
            $table->date("maturity_on")->nullable()->default('0000-00-00');
            $table->date("receipt_started_on")->nullable()->default('0000-00-00');
            $table->date("receipt_ended_on")->nullable()->default('0000-00-00');
            $table->double("contract_amount");
            $table->double("balance");
            $table->float("application_interest_rate");
            $table->integer("contract_deposit_count");
            $table->integer("contract_payment_count");
            $table->integer("scheduled_count");
            $table->integer("not_deposited_count");
            $table->double("after_term_balance");
            $table->double("interest_amount");
            $table->double("interest_tax");
            $table->double("national_tax");
            $table->double("local_tax");
            $table->double("after_tax_interest");
            $table->date("second_created_base_on")->nullable()->default('0000-00-00');
            $table->integer("created_base_month");
            $table->integer("head_count");
            $table->integer("monthly_id")->index();
            $table->double("key_account_number")->index();
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
