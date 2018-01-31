<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameCreateMonthToJifuriTradingFiles extends Migration
{
  
    public $tableName = 'jifuri_trading_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'created_month'))
            {
                $table->renameColumn('created_month', 'create_month');
            }
        });
    }

    public function down() {
        
    }
}
