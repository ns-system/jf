<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNameCollectionLedgers extends Migration
{

    public $tableName = 'name_collection_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("prefecture_code")->index();
            $table->integer("organization_code")->index();
            $table->integer("store_number")->index();
            $table->integer("small_store_number")->index();
            $table->integer("customer_number")->index();
            $table->integer("subject_code")->index();
            $table->integer("category_code")->index();
            $table->integer("assist_product_code")->index();
            $table->integer("name_collection_customer_number")->index();
            $table->integer("account_count");
            $table->double("month_end_balance");
            $table->double("month_end_overdraft_balance");
            $table->double("monthly_cumulation");
            $table->double("year_cumulation_1");
            $table->double("year_cumulation_2");
            $table->double("monthly_overdraft_cumulation");
            $table->double("year_overdraft_cumulation_1");
            $table->double("year_overdraft_cumulation_2");
            $table->char("previous_prefecture_code", 1);
            $table->char("spare_1", 16);
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
