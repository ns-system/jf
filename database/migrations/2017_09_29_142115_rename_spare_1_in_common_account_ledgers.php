<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameSpare1InCommonAccountLedgers extends Migration
{

    public $tableName = 'common_account_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'spare_1'))
            {
                $table->renameColumn('spare_1', 'common_spare_1');
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'common_spare_1'))
            {
                $table->renameColumn('common_spare_1', 'spare_1');
            }
        });
    }

}
