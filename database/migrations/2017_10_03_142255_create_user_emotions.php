<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserEmotions extends Migration
{

    public $tableName = 'user_emotions';
    public $connect   = 'mysql_nikocale';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer('user_id')->index();
            $table->integer('emotion')->index();
            $table->string('comment');
            $table->date('entered_on')->index();
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
