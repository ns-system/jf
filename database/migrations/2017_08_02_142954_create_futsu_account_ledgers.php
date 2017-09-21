<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFutsuAccountLedgers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public $tableName = 'futsu_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->double("account_number")->index();
            $table->integer("contract_number")->index();
            $table->double("surface_balance");
            $table->double("bankbook_balance");
            $table->double("other_bank_ticket_amount");
            $table->double("past_month_surface_balance");
            $table->double("overdraft_fixed_term_limit_amount");
            $table->double("overdraft_fixed_term_interest");
            $table->double("expected_interest");
            $table->double("not_purpose_expected_interest");
            $table->double("not_purpose_payment_amount");
            $table->date("last_transfered_on")->nullable();
            $table->float("special_interest_rate");
            $table->integer("swing_contract_state");
            $table->integer("aggregated_date");
            $table->date("aggregated_start_on")->nullable();
            $table->double("monthly_debtor_amount");
            $table->double("monthly_credit_amount");
            $table->integer("actual_unrecorded_count");
            $table->date("last_recorded_on")->nullable();
            $table->float("overdraft_preferential_interest_rate");
            $table->integer("overdraft_product_code")->index();
            $table->date("overdraft_contracted_on")->nullable();
            $table->date("overdraft_contract_canceled_on")->nullable();
            $table->date("overdraft_deadline_on")->nullable();
            $table->double("overdraft_limit_amount");
            $table->float("overdraft_interest_rate");
            $table->double("collected_delayed_damage");
            $table->double("uncollected_delayed_damage");
            $table->date("term_profit_loss_on")->nullable();
            $table->date("term_profit_loss_set_on")->nullable();
            $table->double("fixed_interest_term_profit_loss");
            $table->date("damage_last_collected_on")->nullable();
            $table->double("balance_set_term_profit_loss");
            $table->char("personal_card_name", 19);
            $table->char("agent_card_name", 19);
            $table->double("teiki_collateral_amount");
            $table->double("teitsumi_collateral_amount");
            $table->double("teiki_number")->index();
            $table->double("teiki_collateral_balance");
            $table->double("teitsumi_number")->index();
            $table->integer("collateral_bankbook_reissue_count");
            $table->double("fixed_collateral_bankbook_balance");
            $table->float("overdraft_preferential_interest_rate_1");
            $table->date("overdraft_preferential_interest_rate_1_applied_on")->nullable();
            $table->float("overdraft_preferential_interest_rate_2");
            $table->date("overdraft_preferential_interest_rate_2_applied_on")->nullable();
            $table->float("overdraft_preferential_interest_rate_3");
            $table->date("overdraft_preferential_interest_rate_3_applied_on")->nullable();
            $table->integer("personal_card_security_state");
            $table->integer("agent_card_security_state");
            $table->char("futsu_spare_1", 90);
            $table->integer("monthly_id")->index();
            $table->timestamps("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
