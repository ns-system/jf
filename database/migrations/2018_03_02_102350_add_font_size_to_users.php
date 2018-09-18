<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFontSizeToUsers extends Migration
{

    public $tableName = 'users';
    public $connect   = 'mysql_laravel';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {

            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'font'))
            {
                $table->string("font")->nullable()->after('user_icon');
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'font_size'))
            {
                $table->integer("font_size")->default(16)->after('font');
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'font_weight'))
            {
                $table->integer("font_weight")->default(500)->after('font_size');
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, 'font_color'))
            {
                $table->string("font_color")->default("#333")->after('font_weight');
            }
        });
    }

    public function down() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'font_size'))
            {
                $table->dropColumn("font_size");
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'font'))
            {
                $table->dropColumn("font");
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'font_color'))
            {
                $table->dropColumn("font_color");
            }
            if (Schema::connection($this->connect)->hasColumn($this->tableName, 'font_weight'))
            {
                $table->dropColumn("font_weight");
            }
        });
    }

}
