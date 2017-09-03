<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZenonCsvFilesToSplitTableColumns extends Migration
{

    public $tableName     = 'zenon_data_csv_files';
    public $connect       = 'mysql_suisin';
    public $is_split      = 'is_split';
    public $foreign_key_1 = 'split_foreign_key_1';
    public $foreign_key_2 = 'split_foreign_key_2';
    public $foreign_key_3 = 'split_foreign_key_3';
    public $foreign_key_4 = 'split_foreign_key_4';

    public function up() {
        Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, $this->is_split))
            {
                $table->boolean($this->is_split)
                        ->after('is_process')
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, $this->foreign_key_1))
            {
                $table->string($this->foreign_key_1)
                        ->after('subject_column_name')
                        ->nullable()
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, $this->foreign_key_2))
            {
                $table->string($this->foreign_key_2)
                        ->after($this->foreign_key_1)
                        ->nullable()
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, $this->foreign_key_3))
            {
                $table->string($this->foreign_key_3)
                        ->after($this->foreign_key_2)
                        ->nullable()
                ;
            }
            if (!Schema::connection($this->connect)->hasColumn($this->tableName, $this->foreign_key_4))
            {
                $table->string($this->foreign_key_4)
                        ->after($this->foreign_key_3)
                        ->nullable()
                ;
            }
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasColumn($this->tableName, $this->is_split))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn($this->is_split);
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, $this->foreign_key_1))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn($this->foreign_key_1);
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, $this->foreign_key_2))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn($this->foreign_key_2);
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, $this->foreign_key_3))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn($this->foreign_key_3);
            });
        }
        if (Schema::connection($this->connect)->hasColumn($this->tableName, $this->foreign_key_4))
        {
            Schema::connection($this->connect)->table($this->tableName, function(Blueprint $table) {
                $table->dropColumn($this->foreign_key_4);
            });
        }
    }

}
