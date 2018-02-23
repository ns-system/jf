<?php

use Illuminate\Database\Seeder;

class ConsignorGroupsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $fake = [
            'id'             => null,
            'create_user_id' => 1,
            'modify_user_id' => 0,
            'created_at'     => date("Y-m-d H:i:s"),
            'updated_at'     => date("Y-m-d H:i:s"),
        ];
        for ($i = 0; $i < 100000; $i++) {
            $fake['group_name'] = 'test_' . sprintf('%07d', $i);
            \DB::connection('mysql_suisin')->table('consignor_groups')->insert($fake);
            if ($i % 10000 == 0)
            {
                echo $i . "executed." . PHP_EOL;
            }
        }
    }

}
