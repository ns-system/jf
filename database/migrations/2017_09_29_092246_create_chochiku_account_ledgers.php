<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChochikuAccountLedgers extends Migration
{

    public $tableName = 'chochiku_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->date("last_recorded_on")->nullable();
            $table->double("surface_balance");
            $table->double("other_bank_ticket_amount");
            $table->date("last_transfered_on")->nullable();
            $table->double("past_month_surface_balance");
            $table->double("bankbook_balance");
            $table->double("futsu_number");
            $table->integer("order_swing_contract_state");
            $table->integer("order_swing_specified_date");
            $table->double("order_swing_monthly_amount");
            $table->double("order_swing_balance");
            $table->integer("inverse_swing_contract_state");
            $table->integer("inverse_swing_specified_on");
            $table->double("inverse_swing_monthly_amount");
            $table->double("inverse_swing_balance");
            $table->double("fixed_interest");
            $table->double("less_than_interest");
            $table->integer("exceed_reduction_state");
            $table->integer("past_month_payment_count");
            $table->integer("payment_count");
            $table->float("special_interest_rate");
            $table->double("swing_fee");
            $table->char("personal_card_name", 19);
            $table->char("agent_card_name", 19);
            $table->integer("personal_card_security_state");
            $table->integer("agent_card_security_state");
            $table->char("spare_1", 27);
            $table->integer("monthly_id")->index();
            $table->integer("subject_code")->index();
            $table->integer("key_account_number")->index();
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
