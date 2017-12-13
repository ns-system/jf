<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeikiAccountLedgers extends Migration
{

    public $tableName = 'teiki_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->double("transfer_payment_number");
            $table->integer("filioparental_state");
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
            $table->integer("tr_state_033");
            $table->integer("tr_state_034");
            $table->integer("tr_state_035");
            $table->integer("tr_state_036");
            $table->integer("tr_state_037");
            $table->integer("tr_state_038");
            $table->integer("tr_state_039");
            $table->integer("tr_state_040");
            $table->integer("tr_state_041");
            $table->integer("tr_state_042");
            $table->integer("tr_state_043");
            $table->integer("tr_state_044");
            $table->integer("tr_state_045");
            $table->integer("tr_state_046");
            $table->integer("tr_state_047");
            $table->integer("tr_state_048");
            $table->integer("teiki_attention_code_1_001");
            $table->integer("teiki_attention_code_1_002");
            $table->integer("teiki_attention_code_1_003");
            $table->integer("teiki_attention_code_1_004");
            $table->integer("teiki_attention_code_1_005");
            $table->integer("teiki_attention_code_1_006");
            $table->integer("teiki_attention_code_1_007");
            $table->integer("teiki_attention_code_1_008");
            $table->integer("teiki_attention_code_1_009");
            $table->integer("teiki_attention_code_1_010");
            $table->integer("teiki_attention_code_1_011");
            $table->integer("teiki_attention_code_1_012");
            $table->integer("teiki_attention_code_1_013");
            $table->integer("teiki_attention_code_1_014");
            $table->integer("teiki_attention_code_1_015");
            $table->integer("teiki_attention_code_1_016");
            $table->integer("teiki_attention_code_2_001");
            $table->integer("teiki_attention_code_2_002");
            $table->integer("teiki_attention_code_2_003");
            $table->integer("teiki_attention_code_2_004");
            $table->integer("teiki_attention_code_2_005");
            $table->integer("teiki_attention_code_2_006");
            $table->integer("teiki_attention_code_2_007");
            $table->integer("teiki_attention_code_2_008");
            $table->integer("teiki_attention_code_2_009");
            $table->integer("teiki_attention_code_2_010");
            $table->integer("teiki_attention_code_2_011");
            $table->integer("teiki_attention_code_2_012");
            $table->integer("teiki_attention_code_2_013");
            $table->integer("teiki_attention_code_2_014");
            $table->integer("teiki_attention_code_2_015");
            $table->integer("teiki_attention_code_2_016");
            $table->integer("teiki_attention_code_3_001");
            $table->integer("teiki_attention_code_3_002");
            $table->integer("teiki_attention_code_3_003");
            $table->integer("teiki_attention_code_3_004");
            $table->integer("teiki_attention_code_3_005");
            $table->integer("teiki_attention_code_3_006");
            $table->integer("teiki_attention_code_3_007");
            $table->integer("teiki_attention_code_3_008");
            $table->integer("teiki_attention_code_3_009");
            $table->integer("teiki_attention_code_3_010");
            $table->integer("teiki_attention_code_3_011");
            $table->integer("teiki_attention_code_3_012");
            $table->integer("teiki_attention_code_3_013");
            $table->integer("teiki_attention_code_3_014");
            $table->integer("teiki_attention_code_3_015");
            $table->integer("teiki_attention_code_3_016");
            $table->double("principal");
            $table->date("teiki_last_traded_on")->nullable();
            $table->date("contracted_on")->nullable();
            $table->date("deposited_on")->nullable();
            $table->integer("teiki_taxation_code")->index();
            $table->float("interest_rate");
            $table->double("paid_amount");
            $table->date("maturity_on")->nullable();
            $table->integer("term_code");
            $table->integer("interest_payment_state");
            $table->float("intermediate_interest_rate");
            $table->integer("continuous_count");
            $table->integer("interest_rate_modify_count");
            $table->float("special_interest_rate");
            $table->date("last_recorded_on")->nullable();
            $table->integer("special_interest_rate_continuous_state");
            $table->integer("contract_tr_record_state_1");
            $table->integer("contract_tr_record_state_2");
            $table->char("spare_1", 190);
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
