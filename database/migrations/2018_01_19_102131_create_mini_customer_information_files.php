<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiniCustomerInformationFiles extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'mini_customer_information_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("prefecture_code")->index();
            $table->integer("organization_code")->index();
            $table->integer("store_number")->index();
            $table->integer("small_store_number")->index();
            $table->integer("subject_code")->index();
            $table->double("account_and_deposit_number")->index();
            $table->integer("type_code")->index();
            $table->integer("customer_number")->index();
            $table->char("kana_name", 15);
            $table->double("actual_balance");
            $table->double("surface_balance");
            $table->double("overdraft_possible_amount");
            $table->date("last_traded_on")->nullable();
            $table->integer("filioparental_state");
            $table->integer("monthly_id")->index();
            $table->double("account_number")->index();
            $table->integer("deposit_number")->index();
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
