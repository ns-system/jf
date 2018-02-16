<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameCollectionCustomerNumberToJifuriTradingFiles extends Migration
{

    public $tableName = 'jifuri_trading_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'name_correction_customer_number'))
            {
                $table->renameColumn('name_correction_customer_number', 'name_collection_number');
            }
        });

        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'name_correction_level'))
            {
                $table->renameColumn('name_correction_LEVEL', 'name_collection_level');
            }
        });
    }

    public function down() {
        
    }

}
