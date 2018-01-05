<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToOldBetsudanAccountLedgers extends Migration
{

    public $tableName = 'old_betsudan_account_ledgers';
    public $connect   = 'mysql_zenon';
    public $keys      = [
        'tr_state_001',
        'tr_state_002',
        'tr_state_003',
        'tr_state_004',
        'tr_state_005',
        'tr_state_006',
        'tr_state_007',
        'tr_state_008',
        'tr_state_009',
        'tr_state_010',
        'tr_state_011',
        'tr_state_012',
        'tr_state_013',
        'tr_state_014',
        'tr_state_015',
        'tr_state_016',
        'tr_state_017',
        'tr_state_018',
        'tr_state_019',
        'tr_state_020',
        'tr_state_021',
        'tr_state_022',
        'tr_state_023',
        'tr_state_024',
        'bill_check_number',
        'bankbook_state',
        'contract_tr_record_state_1',
        'contract_tr_record_state_2',
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
