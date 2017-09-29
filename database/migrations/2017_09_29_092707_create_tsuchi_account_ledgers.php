<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsuchiAccountLedgers extends Migration
{

    public $tableName = 'tsuchi_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->double("transfer_payment_number")->index();
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
            $table->integer("tr_state_025");
            $table->integer("tr_state_026");
            $table->integer("tr_state_027");
            $table->integer("tr_state_028");
            $table->integer("tr_state_029");
            $table->integer("tr_state_030");
            $table->integer("tr_state_031");
            $table->integer("tr_state_032");
            $table->integer("tsuchi_attention_code_1_001");
            $table->integer("tsuchi_attention_code_1_002");
            $table->integer("tsuchi_attention_code_1_003");
            $table->integer("tsuchi_attention_code_1_004");
            $table->integer("tsuchi_attention_code_1_005");
            $table->integer("tsuchi_attention_code_1_006");
            $table->integer("tsuchi_attention_code_1_007");
            $table->integer("tsuchi_attention_code_1_008");
            $table->integer("tsuchi_attention_code_1_009");
            $table->integer("tsuchi_attention_code_1_010");
            $table->integer("tsuchi_attention_code_1_011");
            $table->integer("tsuchi_attention_code_1_012");
            $table->integer("tsuchi_attention_code_1_013");
            $table->integer("tsuchi_attention_code_1_014");
            $table->integer("tsuchi_attention_code_1_015");
            $table->integer("tsuchi_attention_code_1_016");
            $table->integer("tsuchi_attention_code_2_001");
            $table->integer("tsuchi_attention_code_2_002");
            $table->integer("tsuchi_attention_code_2_003");
            $table->integer("tsuchi_attention_code_2_004");
            $table->integer("tsuchi_attention_code_2_005");
            $table->integer("tsuchi_attention_code_2_006");
            $table->integer("tsuchi_attention_code_2_007");
            $table->integer("tsuchi_attention_code_2_008");
            $table->integer("tsuchi_attention_code_2_009");
            $table->integer("tsuchi_attention_code_2_010");
            $table->integer("tsuchi_attention_code_2_011");
            $table->integer("tsuchi_attention_code_2_012");
            $table->integer("tsuchi_attention_code_2_013");
            $table->integer("tsuchi_attention_code_2_014");
            $table->integer("tsuchi_attention_code_2_015");
            $table->integer("tsuchi_attention_code_2_016");
            $table->integer("tsuchi_attention_code_3_001");
            $table->integer("tsuchi_attention_code_3_002");
            $table->integer("tsuchi_attention_code_3_003");
            $table->integer("tsuchi_attention_code_3_004");
            $table->integer("tsuchi_attention_code_3_005");
            $table->integer("tsuchi_attention_code_3_006");
            $table->integer("tsuchi_attention_code_3_007");
            $table->integer("tsuchi_attention_code_3_008");
            $table->integer("tsuchi_attention_code_3_009");
            $table->integer("tsuchi_attention_code_3_010");
            $table->integer("tsuchi_attention_code_3_011");
            $table->integer("tsuchi_attention_code_3_012");
            $table->integer("tsuchi_attention_code_3_013");
            $table->integer("tsuchi_attention_code_3_014");
            $table->integer("tsuchi_attention_code_3_015");
            $table->integer("tsuchi_attention_code_3_016");
            $table->double("principal");
            $table->date("tsuchi_last_traded_on")->nullable();
            $table->date("contracted_on")->nullable();
            $table->date("deposited_on")->nullable();
            $table->float("interest_rate");
            $table->integer("grace_term");
            $table->integer("tsuchi_taxation_code");
            $table->date("last_recorded_on")->nullable();
            $table->float("extra_interest_rate");
            $table->integer("auto_cancellation");
            $table->integer("tr_record_state_1");
            $table->integer("tr_record_state_2");
            $table->char("spare_1", 226);
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
