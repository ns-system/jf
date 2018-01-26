<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCenterCutFiles extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'center_cut_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("original_prefecture_code")->index();
            $table->integer("original_organization_code")->index();
            $table->integer("original_store_number")->index();
            $table->integer("small_store_number")->index();
            $table->integer("subject_code")->index();
            $table->double("account_and_deposit_number")->index();
            $table->integer("type_code")->index();
            $table->integer("customer_number")->index();
            $table->char("kana_name", 30);
            $table->double("actual_balance");
            $table->double("surface_balance");
            $table->double("overdraft_possible_amount");
            $table->date("last_traded_on")->nullable();
            $table->integer("operation_time");
            $table->integer("trade_state")->index();
            $table->integer("handle_state")->index();
            $table->integer("correction_state")->index();
            $table->integer("finance_state")->index();
            $table->double("trade_amount");
            $table->integer("denomination")->index();
            $table->date("previous_last_traded_on")->nullable();
            $table->integer("closed_state")->index();
            $table->date("reckoned_on")->nullable();
            $table->float("interest_rate");
            $table->integer("taxation_code")->index();
            $table->double("new_principal");
            $table->integer("category_code")->index();
            $table->integer("product_code")->index();
            $table->integer("handle_type")->index();
            $table->integer("terminal_type")->index();
            $table->integer("activation_state")->index();
            $table->integer("qualification_code")->index();
            $table->integer("personality_code")->index();
            $table->double("address_code")->index();
            $table->integer("handle_prefecture_code")->index();
            $table->integer("handle_store_number")->index();
            $table->double("toza_payment_amount");
            $table->double("toza_deposit_amount");
            $table->double("payment_interest_amount");
            $table->double("overdraft_interest_amount");
            $table->double("tax_amount");
            $table->integer("deposit_accept_count");
            $table->integer("deposit_payment_count");
            $table->integer("gist_code")->index();
            $table->char("gist_half_kana", 30);
            $table->char("spare_1", 18);
            $table->char("spare_2", 8);
            $table->integer("filioparental_state")->index();
            $table->double("account_number")->index();
            $table->integer("deposit_number")->index();
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
