<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeTradingFiles extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'exchange_trading_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("record_code")->index();
            $table->integer("subject_code")->index();
            $table->integer("send_prefecture_code")->index();
            $table->integer("send_organization_code")->index();
            $table->integer("send_store_number")->index();
            $table->integer("receive_prefecture_code")->index();
            $table->integer("receive_organization_code")->index();
            $table->integer("receive_store_number")->index();
            $table->integer("register_prefecture_code")->index();
            $table->integer("register_organization_code")->index();
            $table->integer("register_store_number")->index();
            $table->char("exchange_serial_number", 7)->index();
            $table->char("1_8_separator", 1);
            $table->char("exchange_type", 4)->index();
            $table->char("supplement_code", 3)->index();
            $table->char("1_16_separator", 1);
            $table->date("jp_exchange_reserve_date")->nullable();
            $table->char("1_25_separator", 1);
            $table->char("exchange_reckon_date", 4);
            $table->char("1_30_separator", 1);
            $table->char("payment_count", 1);
            $table->char("1_32_separator", 1);
            $table->char("exchange_inquiry_number", 16);
            $table->char("exchange_receive_bank", 15)->index();
            $table->char("2_16_separator", 1);
            $table->char("exchange_receive_store", 15)->index();
            $table->char("2_32_separator", 1);
            $table->char("exchange_send_number", 16)->index();
            $table->char("exchange_send_bank", 15)->index();
            $table->char("3_16_separator", 1);
            $table->char("exchange_send_store", 15)->index();
            $table->char("3_32_separator", 1);
            $table->char("exchange_amount", 16)->index();
            $table->char("exchange_interbank_fee", 7);
            $table->char("fee_error", 1);
            $table->char("exchange_number", 16)->index();
            $table->char("edi_number", 20);
            $table->char("other_exchange_store", 4);
            $table->char("exchange_telegram_5", 48)->index();
            $table->char("exchange_telegram_6", 48)->index();
            $table->char("exchange_telegram_7", 48)->index();
            $table->char("exchange_telegram_8_40", 40)->index();
            $table->char("exchange_telegram_8_8", 8)->index();
            $table->char("message", 31);
            $table->char("9_32_separator", 1);
            $table->char("group_center_carry_over", 1);
            $table->char("center_execution_time", 15);
            $table->char("receipter_name", 30);
            $table->double("account_number")->index();
            $table->integer("trade_code")->index();
            $table->integer("toritugi_or_main_state")->index();
            $table->integer("interlock_subject_code")->index();
            $table->integer("interlock_account_number")->index();
            $table->integer("withdrawal_subject_code")->index();
            $table->integer("withdrawal_account_number_number")->index();
            $table->integer("cash_trade")->index();
            $table->integer("commencement_number")->index();
            $table->char("journalizing_number", 6)->index();
            $table->integer("fee");
            $table->char("previous_prefecture_code", 1);
            $table->integer("other_bank_card_transfer_deposit")->index();
            $table->char("spare", 156);
            $table->char("atm_number", 8);
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
