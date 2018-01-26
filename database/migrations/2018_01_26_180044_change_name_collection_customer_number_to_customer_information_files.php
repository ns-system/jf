<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameCollectionCustomerNumberToCustomerInformationFiles extends Migration
{

    public $tableName = 'customer_information_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'name_correction_customer_number'))
            {
                $table->renameColumn('name_correction_customer_number', 'name_collection_customer_number');
            }
        });
    }

    public function down() {
        
    }

}
