<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFloatInterestFiles extends Migration
{

    public $tableName = 'float_interest_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("create_month")->index();
            $table->integer("prefecture_code")->index();
            $table->integer("float_interest_rate_product_state")->index();
            $table->char("product_name", 24);
            $table->integer("guideline_interest_rate_state")->index();
            $table->integer("spread_application_state")->index();
            $table->integer("allotment_limit_rate");
            $table->integer("allotment_fix_year_count");
            $table->integer("new_review_day_state")->index();
            $table->integer("new_review_year");
            $table->integer("new_review_month_01");
            $table->integer("new_review_month_02");
            $table->integer("new_review_month_03");
            $table->integer("new_review_month_04");
            $table->integer("new_review_month_05");
            $table->integer("new_review_month_06");
            $table->integer("new_review_day");
            $table->float("new_interest_rate_amplitude");
            $table->integer("new_application_day_state")->index();
            $table->integer("new_application_day");
            $table->integer("exist_review_day_state")->index();
            $table->integer("exist_review_month_01");
            $table->integer("exist_review_month_02");
            $table->integer("exist_review_month_03");
            $table->integer("exist_review_month_04");
            $table->integer("exist_review_month_05");
            $table->integer("exist_review_month_06");
            $table->integer("exist_review_day");
            $table->float("exist_interest_rate_amplitude");
            $table->integer("exist_application_day_state")->index();
            $table->integer("exist_application_day");
            $table->date("last_approval_ modified_on")->nullable();
            $table->date("approval_ modified_on_01")->nullable();
            $table->float("product_spread_interest_rate_01");
            $table->date("approval_ modified_on_02")->nullable();
            $table->float("product_spread_interest_rate_02");
            $table->date("approval_ modified_on_03")->nullable();
            $table->float("product_spread_interest_rate_03");
            $table->char("spare", 74);
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
