<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddControlStoresToPrefectureCode extends Migration
{

    public $tableName = 'control_stores';
    public $connect   = 'mysql_master';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'prefecture_code'))
            {
                $table->integer('prefecture_code')
                        ->after('id')
                        ->index()
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'prefecture_code'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn('prefecture_code');
            });
        }
    }

}
