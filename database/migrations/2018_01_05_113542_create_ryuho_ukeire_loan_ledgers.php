<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRyuhoUkeireLoanLedgers extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'ryuho_ukeire_loan_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("common_id")->unsigned()->index();
            $table->double("loan_account_number")->index();
            $table->date("interest_cumulation_renewed_on")->nullable()->index();
            $table->double("interest_cumulation");
            $table->double("fixed_interest");
            $table->date("next_contracted_on")->nullable();
            $table->double("contract_handle_term_cumulation");
            $table->date("contract_term_ended_on")->nullable();
            $table->char("local_year", 1);
            $table->char("noko_decide_number", 12);
            $table->float("contract_interest_rate");
            $table->integer("interest_rate_modify_state_1")->index();
            $table->date("interest_rate_modified_1_on")->nullable();
            $table->float("modify_interest_rate_1");
            $table->integer("interest_rate_modify_state_2")->index();
            $table->date("interest_rate_modified_2_on")->nullable();
            $table->float("modify_interest_rate_2");
            $table->integer("interest_rate_modify_state_3")->index();
            $table->date("interest_rate_modified_3_on")->nullable();
            $table->float("modify_interest_rate_3");
            $table->date("previous_paid_on")->nullable();
            $table->date("previous_interest_repaid_on")->nullable();
            $table->date("first_interest_repaid_on")->nullable();
            $table->date("spread_modify_1_on")->nullable();
            $table->float("spread_modify_interest_rate_1");
            $table->date("spread_modify_2_on")->nullable();
            $table->float("spread_modify_interest_rate_2");
            $table->string("spare");
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
