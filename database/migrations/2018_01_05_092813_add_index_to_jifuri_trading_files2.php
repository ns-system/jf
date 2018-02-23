<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToJifuriTradingFiles2 extends Migration
{

    public $tableName = 'jifuri_trading_files';
    public $connect   = 'mysql_zenon';
    public $keys      = [
        'created_month',
        'prefecture_code',
        'organization_code',
        'account_state',
        'name_correction_level',
        'modify_state',
        'impossible_reason_state',
        'union_state',
    ];

    public function checkIndex($indexes, $key) {
        foreach ($indexes as $index) {
            if ($index->Column_name == $key)
            {
                return true;
            }
        }
        return false;
    }

    public function up() {

        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            $indexes = \DB::connection($this->connect)->select(\DB::raw("SHOW INDEX FROM {$this->tableName};"));
            foreach ($this->keys as $key) {
                if (!$this->checkIndex($indexes, $key))
                {
                    $table->index($key);
                }
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            $indexes = \DB::connection($this->connect)->select(\DB::raw("SHOW INDEX FROM {$this->tableName};"));
            foreach ($this->keys as $key) {
                if ($this->checkIndex($indexes, $key))
                {
                    $table->dropIndex([$key]);
                }
            }
        });
    }

}
