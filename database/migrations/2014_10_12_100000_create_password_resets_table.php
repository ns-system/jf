<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordResetsTable extends Migration
{
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'password_resets';
    public $connect   = 'mysql_laravel';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->string('email')->index();
            $table->string('token')->index();
            $table->timestamp('created_at');
        });
    }

    /**
     * マイグレーションを戻す
     *
     * @return void
     */
    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }
}
