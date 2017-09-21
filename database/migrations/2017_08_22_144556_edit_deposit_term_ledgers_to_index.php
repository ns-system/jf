<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditDepositTermLedgersToIndex extends Migration
{

    public $tableName = 'deposit_term_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {

        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            $indexes = \DB::connection($this->connect)->select(\DB::raw("SHOW INDEX FROM {$this->tableName};"));

            $key = 'second_store_number';
            if ($this->checkIndex($indexes, $key))
            {
                $table->dropIndex([$key]);
            }
            $key = 'second_account_number';
            if ($this->checkIndex($indexes, $key))
            {
                $table->dropIndex([$key]);
            }

            $key = 'prefecture_code';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }

            $key = 'store_number';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
            $key = 'account_number';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
            $key = 'contract_number';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
            $key = 'small_store_number';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
            $key = 'term_on';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
            $key = 'subject_code';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
            $key = 'deposit_number';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
            $key = 'category_code';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
            $key = 'assist_product_code';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }

            $key = 'customer_number';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
            $key = 'monthly_id';
            if (!$this->checkIndex($indexes, $key))
            {
                $table->index($key);
            }
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
