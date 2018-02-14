<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJukoResultFiles extends Migration
{

    public $tableName = 'juko_result_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("record_code")->index();
            $table->integer("withdrawal_bank_number")->index();
            $table->integer("withdrawal_bank_store_number")->index();
            $table->integer("deposit_type")->index();
            $table->integer("account_number")->index();
            $table->char("customer_number", 20)->index();
            $table->integer("transfer_payment_result_code")->index();
            $table->char("main_debtor_name", 20);
            $table->char("phone_number", 13);
            $table->char("credit_number", 20)->index();
            $table->char("contact_phone_number", 13);
            $table->integer("security_state")->index();
            $table->double("loan_balance");
            $table->double("schedule_loan_balance");
            $table->integer("bonus_redemption_month");
            $table->integer("redemption_count")->index();
            $table->integer("schedule_redemption_count")->index();
            $table->date("before_contracted_on")->nullable();
            $table->double("delay_principal_and_interest");
            $table->integer("delayed_damage");
            $table->integer("delayed_damage_unit_price");
            $table->char("supare", 83);
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
