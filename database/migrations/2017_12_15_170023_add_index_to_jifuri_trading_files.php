<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToJifuriTradingFiles extends Migration
{

    public $tableName = 'jifuri_trading_files';
    public $connect   = 'mysql_zenon';

    public function up() {

        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            $indexes = \DB::connection($this->connect)->select(\DB::raw("SHOW INDEX FROM {$this->tableName};"));

            $key = 'key_account_number';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
        });
    }

    public function checkIndex($indexes, $key) {
        foreach ($indexes as $index) {
            if ($index->Column_name == $key)
            {
                return true;
            }
        }
        return false;
    }

    public function down() {
        
    }

}
