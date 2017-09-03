<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQualificationCodesToQualificationType extends Migration
{

    public $tableName = 'qualification_codes';
    public $connect   = 'mysql_master';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
//            $table->increments("id");
//            $table->integer("gist_code")->index();
//            $table->string("display_gist");
//            $table->string("zenon_gist");
//            $table->string("keizai_gist_kanji");
//            $table->string("keizai_gist_half_kana");
//            $table->string("keizai_gist_full_kana");
//            $table->timestamps("");
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'qualification_type'))
            {
                $table->string('qualification_type')
                        ->after('qualification_code')
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'qualification_type'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('qualification_type');
            });
        }
    }

}
