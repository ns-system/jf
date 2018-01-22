<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCommonAccountLedgers extends Migration
{

    public $tableName = 'common_account_ledgers';
    public $connect   = 'mysql_zenon';
    public $keys      = [
        'prefecture_code',
        'organization_code',
        'attention_code_1_001',
        'attention_code_1_002',
        'attention_code_1_006',
        'attention_code_2_001',
        'attention_code_2_002',
        'attention_code_2_003',
        'attention_code_2_004',
        'attention_code_2_005',
        'attention_code_2_006',
        'attention_code_2_007',
        'attention_code_3_001',
        'attention_code_3_002',
        'attention_code_3_003',
        'attention_code_3_004',
        'attention_code_3_005',
        'attention_code_3_007',
        'attention_code_3_009',
        'record_state_1',
        'record_state_2',
        'card_issue_state',
        'bankbook_deed_type',
        'account_management_store_number',
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
