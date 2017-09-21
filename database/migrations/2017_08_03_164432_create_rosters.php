<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRosters extends Migration
{

    public $tableName = 'rosters';
    public $connect   = 'mysql_roster';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('plan_work_type_id');
            $table->integer('plan_rest_reason_id')->nullable();
            $table->string('plan_overtime_reason');
            $table->time('plan_overtime_start_time')->nullable();
            $table->time('plan_overtime_end_time')->nullable();
            $table->integer('actual_work_type_id');
            $table->integer('actual_rest_reason_id')->nullable();
            $table->string('actual_overtime_reason');
            $table->time('actual_overtime_start_time')->nullable();
            $table->time('actual_overtime_end_time')->nullable();
            $table->boolean('is_plan_accept');
            $table->boolean('is_plan_reject');
            $table->boolean('is_plan_verify');
            $table->integer('plan_accept_user_id');
            $table->integer('plan_reject_user_id');
            $table->integer('plan_verify_user_id');
            $table->dateTime('plan_accepted_at');
            $table->dateTime('plan_rejected_at');
            $table->dateTime('plan_verified_at');
            $table->boolean('is_actual_accept');
            $table->boolean('is_actual_reject');
            $table->boolean('is_actual_verify');
            $table->integer('actual_accept_user_id');
            $table->integer('actual_reject_user_id');
            $table->integer('actual_verify_user_id');
            $table->dateTime('actual_accepted_at');
            $table->dateTime('actual_rejected_at');
            $table->dateTime('actual_verified_at');
            $table->boolean('is_process');
            $table->integer('process_user_id');
            $table->integer('month_id');
            $table->timestamps('');
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
