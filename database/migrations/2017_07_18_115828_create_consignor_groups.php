<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsignorGroups extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'consignor_groups';
    public $connect   = 'mysql_suisin';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
//            $table->integer("consignor_group_number")->index();
            $table->string("group_name");
            $table->integer("create_user_id");
            $table->integer("modify_user_id");
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
