<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsumiteiHistoryFiles extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'tsumitei_history_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("record_code")->index();
            $table->integer("prefecture_code")->index();
            $table->integer("organization_code")->index();
            $table->integer("store_number")->index();
            $table->integer("small_store_number")->index();
            $table->integer("customer_number")->index();
            $table->integer("subject_code")->index();
            $table->double("account_number")->index();
            $table->integer("is_dead_account")->index();
            $table->date("account_created_on")->nullable();
            $table->date("last_traded_on")->nullable();
            $table->date("last_handled_on")->nullable();
            $table->date("last_recorded_on")->nullable();
            $table->integer("record_state_1");
            $table->integer("record_state_2");
            $table->integer("category_code")->index();
            $table->integer("product_code")->index();
            $table->integer("assist_product_code")->index();
            $table->double("total_deposit_amount");
            $table->float("interval_ganka_interest_rate");
            $table->integer("deposit_state")->index();
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
            $table->date("last_maturity_on")->nullable();
            $table->date("last_auto_continued_on")->nullable();
            $table->integer("stop_state")->index();
            $table->integer("jifuri_stop_state")->index();
            $table->integer("auto_cancellation_state")->index();
            $table->double("related_account_number")->index();
            $table->integer("save_up_bonus_date_1");
            $table->integer("save_up_bonus_date_2");
            $table->integer("save_up_bonus_date_3");
            $table->integer("save_up_bonus_date_4");
            $table->double("save_up_bonus_amount_1");
            $table->double("save_up_bonus_amount_2");
            $table->double("save_up_bonus_amount_3");
            $table->double("save_up_bonus_amount_4");
            $table->date("traded_on")->nullable();
            $table->date("handled_on")->nullable();
            $table->date("deposited_on")->nullable();
            $table->integer("is_deposit_or_new");
            $table->integer("is_correction_or_cancellation");
            $table->integer("is_interval_ganka_interest");
            $table->integer("is_total");
            $table->integer("is_deposit_correction");
            $table->integer("is_closed");
            $table->integer("is_resend");
            $table->integer("is_new");
            $table->integer("is_not_purpose_payment_amount");
            $table->integer("is_tax_payment");
            $table->integer("is_accident");
            $table->integer("is_recorded");
            $table->integer("is_paid");
            $table->integer("is_interest_calculated");
            $table->integer("is_agent_usage");
            $table->integer("is_aggregate");
            $table->integer("is_jef_character_usage");
            $table->integer("is_center");
            $table->integer("is_auto_loan");
            $table->integer("is_card_issue_fee");
            $table->integer("is_card_loan");
            $table->integer("is_auto_continued");
            $table->integer("is_aggregate_interest");
            $table->integer("is_part_paid");
            $table->integer("is_total_debit_and_credit");
            $table->integer("is_gist_kanji_exist");
            $table->integer("is_simultaneously_exist");
            $table->integer("is_debit_payoff");
            $table->integer("is_debit_cancellation");
            $table->integer("is_atm");
            $table->integer("is_new_tsumitei");
            $table->integer("is_two_line");
            $table->integer("is_store_reason");
            $table->integer("is_not_balance_trade");
            $table->integer("is_auto_continuous");
            $table->integer("is_interval_interest_payment");
            $table->integer("is_rough_amount_cancellation");
            $table->integer("is_payment_collected");
            $table->integer("is_trade_amount_not_edit");
            $table->integer("is_new_history");
            $table->char("message_number", 5);
            $table->integer("handle_prefecture_code")->index();
            $table->integer("handle_store_number")->index();
            $table->integer("handle_organization_code")->index();
            $table->integer("trade_code")->index();
            $table->integer("denomination")->index();
            $table->integer("denomination_detail")->index();
            $table->integer("other_store_state");
            $table->double("deposit_amount");
            $table->float("rate");
            $table->date("part_payment_maturity_on")->nullable();
            $table->date("new_tsumitei_maturity_on")->nullable();
            $table->date("next_interval_maturity_on")->nullable();
            $table->char("spare_1", 26);
            $table->string("spare_2");
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
