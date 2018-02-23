<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuidelineInterestFiles extends Migration
{

    public $tableName = 'guideline_interest_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("create_month");
            $table->integer("prefecture_code")->index();
            $table->integer("organization_code")->index();
            $table->integer("guideline_interest_rate_state")->index();
            $table->date("last_approved_base_on")->nullable();
            $table->date("unapproved_base_on")->nullable();
            $table->float("unapproved_guideline_interest_rate");
            $table->date("base_on_01")->nullable();
            $table->float("interest_rate_01");
            $table->date("base_on_02")->nullable();
            $table->float("interest_rate_02");
            $table->date("base_on_03")->nullable();
            $table->float("interest_rate_03");
            $table->date("base_on_04")->nullable();
            $table->float("interest_rate_04");
            $table->date("base_on_05")->nullable();
            $table->float("interest_rate_05");
            $table->date("base_on_06")->nullable();
            $table->float("interest_rate_06");
            $table->date("base_on_07")->nullable();
            $table->float("interest_rate_07");
            $table->date("base_on_08")->nullable();
            $table->float("interest_rate_08");
            $table->date("base_on_09")->nullable();
            $table->float("interest_rate_09");
            $table->date("base_on_10")->nullable();
            $table->float("interest_rate_10");
            $table->date("base_on_11")->nullable();
            $table->float("interest_rate_11");
            $table->date("base_on_12")->nullable();
            $table->float("interest_rate_12");
            $table->date("base_on_13")->nullable();
            $table->float("interest_rate_13");
            $table->date("base_on_14")->nullable();
            $table->float("interest_rate_14");
            $table->date("base_on_15")->nullable();
            $table->float("interest_rate_15");
            $table->date("base_on_16")->nullable();
            $table->float("interest_rate_16");
            $table->date("base_on_17")->nullable();
            $table->float("interest_rate_17");
            $table->date("base_on_18")->nullable();
            $table->float("interest_rate_18");
            $table->date("base_on_19")->nullable();
            $table->float("interest_rate_19");
            $table->date("base_on_20")->nullable();
            $table->float("interest_rate_20");
            $table->date("base_on_21")->nullable();
            $table->float("interest_rate_21");
            $table->date("base_on_22")->nullable();
            $table->float("interest_rate_22");
            $table->date("base_on_23")->nullable();
            $table->float("interest_rate_23");
            $table->date("base_on_24")->nullable();
            $table->float("interest_rate_24");
            $table->date("base_on_25")->nullable();
            $table->float("interest_rate_25");
            $table->date("base_on_26")->nullable();
            $table->float("interest_rate_26");
            $table->date("base_on_27")->nullable();
            $table->float("interest_rate_27");
            $table->date("base_on_28")->nullable();
            $table->float("interest_rate_28");
            $table->date("base_on_29")->nullable();
            $table->float("interest_rate_29");
            $table->date("base_on_30")->nullable();
            $table->float("interest_rate_30");
            $table->date("base_on_31")->nullable();
            $table->float("interest_rate_31");
            $table->char("spare", 42);
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
