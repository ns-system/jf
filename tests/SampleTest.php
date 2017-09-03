<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SampleTest extends TestCase
{

    use DatabaseTransactions;

    protected $csv_file = 'C:/Users/n-teshima/Desktop/import_test/委託者マスタ.csv';

    /**
     * ログイン前ユーザーでアクセスし、ユーザー登録を通すテスト
     * @test
     */
    public function ユーザー登録() {
        $this->visit('/')
                ->seePageIs('/')
                ->see('ユーザー登録')
                ->type('test@test.com', 'email')
                ->type('test', 'name')
                ->type('sample', 'password')
                ->type('sample', 'password_confirmation')
                ->press('登録')
                ->seePageIs('/')
        ;
    }

    /**
     * ログイン後ユーザーでアクセスし、Permission_errorページへ誘導するテスト
     * @test
     */
    public function 非管理ユーザーとしてアクセス() {
//        $user = factory(\App\User::class)->create();
//        $user = \App\User::find(1);
        $user = factory(\App\User::class)->create();
        $this->actingAs($user)
                ->visit('/')
                ->seePageIs('/')
                ->visit('/admin/admin_user')
                ->seePageIs('/permission_error')
        ;
    }

    /**
     * ログイン後ユーザーでアクセスし、Permission_errorページへ誘導するテスト
     * @test
     */
    public function 管理ユーザーとしてアクセスして検索する() {
//        $user = factory(\App\User::class)->create();
        $user = \App\User::find(1);
//        $user = factory(\App\User::class)->create();
//        var_dump($user);
        $this->actingAs($user)
                ->visit('/admin/admin_user')
                ->type('sample', 'name')
                ->press('検索する')
                ->type('', 'name')
                ->type('sample', 'mail')
                ->press('検索する')
                ->type('', 'mail')
                ->select('0', 'super')
                ->press('検索する')
                ->select('1', 'super')
                ->press('検索する')
                ->select('', 'super')
                ->select('0', 'suisin')
                ->press('検索する')
                ->select('1', 'suisin')
                ->press('検索する')
                ->select('', 'suisin')
                ->press('検索する')
                ->select('0', 'roster')
                ->press('検索する')
                ->select('1', 'roster')
                ->press('検索する')
                ->visit('/admin/admin_user')
                ->visit('/admin/admin_user/18')
                ->select('1', 'is_super_user')
                ->press('更新する')
                ->seePageIs('/admin/admin_user')
        ;
    }

    /**
     * ログイン後ユーザーでアクセスし、Permission_errorページへ誘導するテスト
     * @test
     */
    public function 管理ユーザーとしてアクセスしてマスタ更新() {
//        $user = factory(\App\User::class)->create();
        $user = \App\User::find(1);
//        $user = factory(\App\User::class)->create();
//        var_dump($user);
        $this->actingAs($user)
                ->visit('/admin/suisin/home')
                ->visit('/admin/suisin/Suisin/Consignor')
                ->attach($this->csv_file, 'csv_file')
//                ->press('ImportCSV')
//                ->seePageIs('/admin/suisin/Suisin/Consignor/import')
//                ->see('CSVデータの取り込みが完了しました。')
//                ->press('更新する')
//                ->seePageIs('/admin/suisin/Suisin/Consignor')
//                ->see('処理が終了しました。')
        ;
    }

}
