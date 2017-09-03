<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepositGistCodesToNullable extends Migration
{

    public $tableName = 'deposit_gist_codes';
    public $connect   = 'mysql_master';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            $column = 'zenon_gist';
            $table->string($column, 255)->nullable()->change();

            $column = 'keizai_gist_kanji';
            $table->string($column, 255)->nullable()->change();

            $column = 'keizai_gist_half_kana';
            $table->string($column, 255)->nullable()->change();

            $column = 'keizai_gist_full_kana';
            $table->string($column, 255)->nullable()->change();
        });
    }

    public function down() {
        
    }

}
