<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTozaAccountLedgers extends Migration
{

    public $tableName = 'toza_account_ledgers';
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
            $table->date("last_recorded_on")->nullable();
            $table->date("last_transfered_on")->nullable();
            $table->date("overdraft_contracted_on")->nullable();
            $table->date("overdraft_contract_deadline_on")->nullable();
            $table->double("overdraft_interest");
            $table->double("overdraft_limit_1");
            $table->float("overdraft_interest_rate_1");
            $table->integer("collateral_code_1")->index();
            $table->double("overdraft_limit_2");
            $table->float("overdraft_interest_rate_2");
            $table->integer("collateral_code_2")->index();
            $table->double("overdraft_limit_3");
            $table->float("overdraft_interest_rate_3");
            $table->integer("collateral_code_3")->index();
            $table->integer("card_personality_state");
            $table->date("term_profit_loss_on")->nullable();
            $table->date("term_profit_loss_set_on")->nullable();
            $table->double("term_profit_loss_balance");
            $table->double("fixed_interest_term_profit_loss");
            $table->date("damage_last_collected_on")->nullable();
            $table->double("collected_delayed_damage");
            $table->integer("total_delay_count");
            $table->date("bankbook_usage_started_on")->nullable();
            $table->date("bankbook_usage_stopped_on")->nullable();
            $table->double("expected_interest");
            $table->float("special_interest_rate");
            $table->float("specified_interest_rate");
            $table->integer("taxation_code");
            $table->integer("overdraft_product_code")->index();
            $table->double("uncollected_delayed_damage");
            $table->date("new_current_first_issued_on")->nullable();
            $table->integer("accident_bill_check_count");
            $table->integer("last_bill_check_tr_number");
            $table->char("personal_card_name", 19);
            $table->char("agent_card_name", 19);
            $table->integer("account_spread_profit_margin");
            $table->integer("type_state");
            $table->integer("card_loan_code")->index();
            $table->integer("anytime_interest_rate_application_state");
            $table->integer("ringi_number");
            $table->double("security_number");
            $table->integer("fee_state");
            $table->float("base_interest_rate");
            $table->integer("interest_rate_preferential_state");
            $table->double("overdraft_limit_amount");
            $table->float("application_interest_rate");
            $table->float("delay_interest_rate");
            $table->integer("stop_continuous_state");
            $table->integer("delay_state");
            $table->double("delay_contract_principal");
            $table->double("delay_contract_interest");
            $table->float("damage_interest_rate");
            $table->float("security_fee_rate");
            $table->double("security_fee");
            $table->double("permit_overdraft_limit_amount");
            $table->date("agreed_fund_association_on")->nullable();
            $table->integer("security_fee_collect_state");
            $table->integer("security_institution_code")->index();
            $table->integer("security_fund_code");
            $table->date("used_deadline_on")->nullable();
            $table->date("last_loaned_on")->nullable();
            $table->date("last_optional_repayment_on")->nullable();
            $table->date("last_contract_repayment_on")->nullable();
            $table->double("management_body_interior_debt");
            $table->double("management_body_external_debt");
            $table->date("subrogation_asked_on")->nullable();
            $table->double("next_planed_repayment_amount");
            $table->integer("personal_card_security_code_state");
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
