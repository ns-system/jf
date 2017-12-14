<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOldBetsudanAccountLedgers extends Migration
{

    public $tableName = 'old_betsudan_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->char("kanji_subject", 20);
            $table->double("surface_balance");
            $table->double("bankbook_balance");
            $table->double("monthly_deposit_amount");
            $table->double("monthly_payment_amount");
            $table->integer("tr_state_001");
            $table->integer("tr_state_002");
            $table->integer("tr_state_003");
            $table->integer("tr_state_004");
            $table->integer("tr_state_005");
            $table->integer("tr_state_006");
            $table->integer("tr_state_007");
            $table->integer("tr_state_008");
            $table->integer("tr_state_009");
            $table->integer("tr_state_010");
            $table->integer("tr_state_011");
            $table->integer("tr_state_012");
            $table->integer("tr_state_013");
            $table->integer("tr_state_014");
            $table->integer("tr_state_015");
            $table->integer("tr_state_016");
            $table->integer("tr_state_017");
            $table->integer("tr_state_018");
            $table->integer("tr_state_019");
            $table->integer("tr_state_020");
            $table->integer("tr_state_021");
            $table->integer("tr_state_022");
            $table->integer("tr_state_023");
            $table->integer("tr_state_024");
            $table->double("principal");
            $table->date("old_betsudan_last_traded_on")->nullable();
            $table->date("contracted_on")->nullable();
            $table->date("deposited_on")->nullable();
            $table->char("gist", 20);
            $table->integer("bill_check_number");
            $table->integer("bankbook_reissue_count");
            $table->date("bankbook_usage_started_on")->nullable();
            $table->date("bankbook_usage_stopped_on")->nullable();
            $table->integer("bankbook_state");
            $table->integer("contract_tr_record_state_1");
            $table->integer("contract_tr_record_state_2");
            $table->char("spare_1", 222);
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
