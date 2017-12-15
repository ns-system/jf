<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsumiteiAccountLedgers extends Migration
{

    public $tableName = 'tsumitei_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->date("last_recorded_on")->nullable();
            $table->double("deposit_amount");
            $table->double("transfer_payment_number");
            $table->integer("deposit_count");
            $table->float("interest_rate");
            $table->integer("deposit_state");
            $table->double("monthly_deposit_amount");
            $table->date("first_deposited_on")->nullable();
            $table->integer("transfer_payment_date");
            $table->date("maturity_on")->nullable();
            $table->double("tr_erasure_amount");
            $table->date("deposit_deadline_on")->nullable();
            $table->integer("contract_term");
            $table->date("last_interest_calculated_on")->nullable();
            $table->date("next_interest_calculated_on")->nullable();
            $table->double("interest_amount");
            $table->integer("auto_continuous_state");
            $table->integer("transfer_payment_rate");
            $table->date("last_maturity_on")->nullable();
            $table->date("last_auto_continued_on")->nullable();
            $table->integer("stop_state");
            $table->integer("jifuri_stop_state");
            $table->integer("auto_cancellation_state");
            $table->double("related_account_number");
            $table->integer("save_up_bonus_date_1");
            $table->integer("save_up_bonus_date_2");
            $table->integer("save_up_bonus_date_3");
            $table->integer("save_up_bonus_date_4");
            $table->double("save_up_bonus_amount_1");
            $table->double("save_up_bonus_amount_2");
            $table->double("save_up_bonus_amount_3");
            $table->double("save_up_bonus_amount_4");
            $table->char("spare_1", 221);
            $table->integer("monthly_id")->index();
            $table->integer("subject_code")->index();
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
