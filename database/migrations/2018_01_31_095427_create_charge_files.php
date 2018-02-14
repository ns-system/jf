<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeFiles extends Migration
{

    public $tableName = 'charge_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("create_month");
            $table->integer("prefecture_code")->index();
            $table->integer("organization_code")->index();
            $table->integer("store_number")->index();
            $table->integer("small_store_number")->index();
            $table->integer("charge_type")->index();
            $table->char("line_number", 10)->index();
            $table->integer("terminal_number")->index();
            $table->integer("subject_code")->index();
            $table->integer("unit_price");
            $table->integer("target_count");
            $table->double("target_amount");
            $table->double("ask_usage_fee_1");
            $table->double("ask_usage_fee_2");
            $table->double("ask_call_fee");
            $table->double("consumption_tax");
            $table->double("work_expense");
            $table->double("packet_count");
            $table->integer("terminal_count");
            $table->integer("store_category")->index();
            $table->integer("terminal_category")->index();
            $table->integer("special_reduction_state")->index();
            $table->double("special_reduction_amount");
            $table->char("previous_prefecture_code", 1);
            $table->char("spare_1", 42);
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
