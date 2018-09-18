<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToLoanTermLedgers extends Migration
{

    public $tableName = 'loan_term_ledgers';
    public $connect   = 'mysql_zenon';
    public $keys      = [
        'data_state',
        'file_state',
        'organization_code',
        'return_prefecture_code',
        'execution_state',
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
