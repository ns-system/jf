<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGistMaster extends Migration
{
    public $tableName = 'deposit_gist_codes';
    public $connect   = 'mysql_master';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("gist_code")->index();
            $table->string("display_gist");
            $table->string("zenon_gist");
            $table->string("keizai_gist_kanji");
            $table->string("keizai_gist_half_kana");
            $table->string("keizai_gist_full_kana");
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
