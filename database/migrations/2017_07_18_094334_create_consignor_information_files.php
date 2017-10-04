<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsignorInformationFiles extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'consignors';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("consignor_code")->index()->unique();
            $table->char("consignor_name", 20);
            $table->date("reference_last_traded_on");
            $table->string("display_consignor_name", 255)->nullable();
            $table->integer("consignor_group_id")->nullable()->index();
            $table->timestamps("");
        });
    }

    /**
     * マイグレーションを戻す
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
