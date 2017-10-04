<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSample extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'sample';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("increment_id");
            $table->integer("id");
            $table->char("name", 255);
            $table->date("created_on");
            $table->integer("store_number");
            $table->integer("subject_code");
            $table->bigInteger("previous_account_number")->index();
            $table->bigInteger("account_number");
            $table->timestamps();
            
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
