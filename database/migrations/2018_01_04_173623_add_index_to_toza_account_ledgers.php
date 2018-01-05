<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTozaAccountLedgers extends Migration
{

    public $tableName = 'toza_account_ledgers';
    public $connect   = 'mysql_zenon';
    public $keys      = [
        'card_personality_state',
        'toza_taxation_code',
        'last_bill_check_tr_number',
        'type_state',
        'ringi_number',
        'security_number',
        'fee_state',
        'interest_rate_preferential_state',
        'stop_continuous_state',
        'delay_state',
        'security_fee_collect_state',
        'security_fund_code',
        'personal_card_security_code_state',
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
