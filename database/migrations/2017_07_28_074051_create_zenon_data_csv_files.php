<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZenonDataCsvFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $tableName = 'zenon_data_csv_files';
    public $connect   = 'mysql_suisin';
    //本番ではconnectを変更する
    public function up()
    {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->string("csv_file_name");
            $table->integer("zenon_data_type_id")->index();
            $table->string("zenon_data_name");
            $table->integer("first_column_position");
            $table->integer("column_length");
            $table->string("reference_return_date");
            $table->boolean("is_daily");
            $table->boolean("is_monthly");
            $table->string("database_name");
            $table->string("table_name");
            $table->boolean("is_cumulative");
            $table->boolean('is_account_convert');
            $table->boolean('is_process');
            $table->integer("zenon_format_id")->index();
            $table->string("account_column_name")->nullable();
            $table->string("subject_column_name")->nullable();
            $table->timestamps("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }
}
