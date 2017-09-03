<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJifuriTradingFiles extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public $tableName = 'jifuri_trading_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("created_month");
            $table->integer("prefecture_code");
            $table->integer("organization_code");
            $table->integer("store_number")->index();
            $table->integer("small_store_number")->index();
            $table->integer("customer_number")->index();
            $table->integer("consignor_code")->index();
            $table->char("plural_constract_number", 20);
            $table->integer("transfer_paytment_customer_number")->index();
            $table->double("transfer_payment_number")->index();
            $table->integer("subject_code")->index();
            $table->integer("account_state");
            $table->char("customer_name", 30);
            $table->integer("area_code")->index();
            $table->integer("name_correction_customer_number")->index();
            $table->integer("name_correction_level");
            $table->char("consignor_name", 20);
            $table->integer("modify_state");
            $table->date("modified_on")->nullable()->default('0000-00-00');
            $table->date("last_traded_on")->nullable()->default('0000-00-00');
            $table->date("jifuri_contracted_on")->nullable()->default('0000-00-00');
            $table->integer("impossible_reason_state");
            $table->integer("union_state");
            $table->date("scheduled_transfer_payment_on")->nullable()->default('0000-00-00');
            $table->integer("previous_prefecture_code");
            $table->char("spare_1", 76);
            $table->integer("monthly_id")->index();
            $table->double("key_account_number");
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
