<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZenonDataTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $tableName = 'zenon_data_types';
    public $connect   = 'mysql_suisin';
    //本番ではconnect変更する
    public function up()
    {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->string("data_type_name");
            $table->timestamps("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }
}
