<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BusinessNetTradingFiles extends Migration
{

    public $tableName = 'business_net_trading_files';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("client_number");
            $table->integer("sequence_number");
            $table->integer("trade_type")->index();
            $table->date("specify_traded_on");
            $table->integer("transfer_from_customer_number")->index();
            $table->integer("transfer_from_subject_code")->index();
            $table->integer("transfer_from_account_number")->index();
            $table->integer("transfer_from_holder");
            $table->integer("transfer_to_bank_code")->index();
            $table->integer("transfer_to_store_number")->index();
            $table->integer("is_transfer_to_register");
            $table->integer("transfer_to_subject_code")->index();
            $table->double("transfer_to_account_number");
            $table->integer("transfer_to_customer_number")->index();
            $table->double("key_account_number")->nullable()->index();
            $table->string("transfer_to_name");
            $table->double("amount");
            $table->integer("key_gist_code")->nullable()->index();
            $table->integer("gist_code");
            $table->string("gist_half_kana");
            $table->string("gist_full_kana_and_kanji");
            $table->integer("reception_number");
            $table->dateTime("recepted_at");
            $table->dateTime("registered_at");
            $table->string("requester_number");
            $table->boolean("is_kawase_later")->index();
            $table->double("after_kawase_fee_amount");
            $table->double("before_kawase_fee_amount");
            $table->boolean("is_keizai")->index();
            $table->double("after_keizai_fee_amount");
            $table->double("before_keizai_fee_amount");
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
