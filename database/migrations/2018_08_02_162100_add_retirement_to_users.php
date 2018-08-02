<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRetirementToUsers extends Migration
{
    public $tableName = 'users';
    public $connect   = 'mysql_laravel';

    public function up()
    {
        Schema::connection($this->connect)->table($this->tableName, function (Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'retirement')) {
                $table->boolean("retirement")->index()->after('is_super_user');
            }
        });
    }

    public function down()
    {
        Schema::connection($this->connect)->table($this->tableName, function (Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'retirement')) {
                $table->dropColumn("retirement");
            }
        });
    }

}
