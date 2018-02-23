<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeStoreFiles extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'exchange_store_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("data_state")->index();
            $table->integer("file_state")->index();
            $table->integer("prefecture_code")->index();
            $table->char("spare", 33);
            $table->date("created_base_on")->nullable();
            $table->integer("return_prefecture_code")->index();
            $table->integer("bank_code")->index();
            $table->integer("bank_store_number")->index();
            $table->integer("organization_code")->index();
            $table->integer("store_number")->index();
            $table->char("kanji_bank_name", 30);
            $table->char("kanji_omit_bank_name", 30);
            $table->char("kanji_store_name", 30);
            $table->char("kana_bank_name", 15);
            $table->char("kana_abbreviation_bank_name", 15);
            $table->char("kana_store_name", 15);
            $table->char("zip_code", 10);
            $table->char("store_location", 80);
            $table->char("kanji_store_location", 110);
            $table->char("phone_number", 17);
            $table->char("official_store_name", 1);
            $table->char("store_type", 1);
            $table->char("clearer_bank_code", 4);
            $table->char("own_center", 1);
            $table->char("transfer_deposit_center", 1);
            $table->char("shute_center", 1);
            $table->char("exchange_center", 1);
            $table->char("not_daite_store", 1);
            $table->char("earthquake_reinforce_area", 2);
            $table->date("changed_on")->nullable();
            $table->date("bank_modified_on")->nullable();
            $table->char("bank_modify_reason", 12);
            $table->date("bank_deleted_on")->nullable();
            $table->date("store_modified_on")->nullable();
            $table->char("store_modify_reason", 12);
            $table->date("store_deleted_on")->nullable();
            $table->date("coexist_started_on")->nullable();
            $table->integer("inherit_unity_bank")->index();
            $table->integer("inherit_unity_store_number")->index();
            $table->date("second_created_base_on")->nullable();
            $table->integer("created_base_month");
            $table->integer("head_count");
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
