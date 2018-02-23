<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCirCreatedOnToCustomerInformationFiles extends Migration
{  public $tableName = 'customer_information_files';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
           
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'cir_created_on'))
            {
                $table->date("cir_created_on")
                        ->nullable()
                        ->after('cir_number');


            }
        });
    }

    public function down() {
       
    }
}
