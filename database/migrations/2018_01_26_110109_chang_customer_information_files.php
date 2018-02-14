<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangCustomerInformationFiles extends Migration
{

    public $tableName = 'customer_information_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'cir_created_on'))
            {
                $table->dropColumn('cir_created_on');
            }
        });
    }

    public function down() {
        
    }

}
