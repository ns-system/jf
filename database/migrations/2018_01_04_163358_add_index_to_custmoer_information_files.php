<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCustmoerInformationFiles extends Migration
{

    public $tableName = 'customer_information_files';
    public $connect   = 'mysql_zenon';
    public $keys      = [
        'prefecture_code',
        'organization_code',
        'customer_number',
        'subject_code',
        'cir_state_001',
        'cir_state_002',
        'cir_state_003',
        'cir_state_004',
        'cir_state_005',
        'cir_state_007',
        'cir_state_010',
        'cir_state_011',
        'cir_state_012',
        'cir_state_013',
        'cir_state_014',
        'cir_state_020',
        'cir_state_026',
        'cir_state_032',
        'cir_state_033',
        'cir_state_034',
        'cir_state_035',
        'cir_state_036',
        'cir_state_037',
        'cir_state_038',
        'cir_state_039',
        'cir_state_040',
        'attention_code_1_001',
        'attention_code_1_002',
        'attention_code_3_001',
        'attention_code_3_002',
        'name_initial',
        'address_state',
        'address_code',
        'zip_code',
        'gender',
        'interest_rate_application_state',
        'staff_code',
        'tax_not_application_state',
        'name_correction_level',
        'loan_target_state',
        'management_aggravation_state',
        'valuation_target_state',
        'joint_deposit_state',
        'my_number_personality_state',
        'corporation_number',
        'foreign_account_state',
        'foreign_account_country_code',
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
