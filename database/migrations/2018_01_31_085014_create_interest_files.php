<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterestFiles extends Migration
{

    public $tableName = 'interest_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("create_month");
            $table->integer("prefecture_code")->index();
            $table->integer("data_type")->index();
            $table->integer("register_state")->index();
            $table->integer("organization_code")->index();
            $table->integer("store_number")->index();
            $table->integer("card_loan_code")->index();
            $table->date("application_started_on")->nullable();
            $table->date("application_ended_on")->nullable();
            $table->integer("subject_code")->index();
            $table->integer("product_code")->index();
            $table->integer("assist_product_code")->index();
            $table->integer("bankbook_deed_state")->index();
            $table->integer("term_code")->index();
            $table->integer("amount_1");
            $table->float("interest_rate_1");
            $table->integer("amount_2");
            $table->float("interest_rate_2");
            $table->integer("amount_3");
            $table->float("interest_rate_3");
            $table->integer("amount_4");
            $table->float("interest_rate_4");
            $table->integer("amount_5");
            $table->float("interest_rate_5");
            $table->integer("amount_6");
            $table->float("interest_rate_6");
            $table->integer("amount_7");
            $table->float("interest_rate_7");
            $table->integer("amount_8");
            $table->float("interest_rate_8");
            $table->integer("amount_9");
            $table->float("interest_rate_9");
            $table->integer("amount_10");
            $table->float("interest_rate_10");
            $table->char("spare", 28);
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
