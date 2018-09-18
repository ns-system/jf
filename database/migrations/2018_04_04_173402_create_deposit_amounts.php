<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositAmounts extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'deposit_amounts';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("common_id")->unsigned()->index();
            $table->integer("customer_number")->index();
            $table->double("balance");
            $table->integer("subject_code")->index();
            $table->double("account_number")->index();
            $table->double("key_account_number")->index();
            $table->double("contract_number")->index();
            $table->integer("monthly_id")->index();
            $table->boolean("is_aggregate")->index();

            $table->integer("filioparental_state")->index()->default(0);
            $table->boolean("is_plus")->index()->default(false);
            $table->integer("tr_state_1")->index()->default(0);
            $table->integer("tr_state_2")->index()->default(0);
//            $table->timestamps("");
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
