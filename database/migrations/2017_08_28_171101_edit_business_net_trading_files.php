<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditBusinessNetTradingFiles extends Migration
{

    public $tableName = 'business_net_trading_files';
    public $connect   = 'mysql_suisin';

    public function up() {

        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'transfer_from_holder'))
            {
                $table->string('transfer_from_holder')->change();
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, 'transfer_from_holder'))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->integer('transfer_from_holder')->change();
            });
        }
    }

}
