<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FuncSuisinAdminControllerTest
 *
 * @author r-kawanishi
 */
use App\Services\Traits;

class FuncSuisinAdminControllerLoanTest extends TestCase
{

    use Traits\CsvUsable;

    protected static $init = false;
    protected $user;

    public function setUp() {
        parent::setUp();

        if (!static::$init)
        {
            static::$init = true;
            try {
                \Artisan::call('db:reset', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('db:create', ['--dbenv' => 'testing', '--hide' => 'true']);
                \Artisan::call('migrate');
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
    }

    //貸付
    //摘要コード 
    /**
     * @tests
     */
    public function 貸付種類を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Category::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanCategory')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCategory')

        ;
    }

    /**
     * @tests
     */
    public function 貸付種類でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Category::truncate();
        $file_name = '貸付種類.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanCategory')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCategory')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCategory/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCategory')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\Category::
                            where('loan_category_code', trim($data[0]))->
                            where('loan_category_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 貸付種類で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Category::truncate();
        $file_name = '貸付種類.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanCategory')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCategory')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCategory')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 貸付種類で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Category::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanCategory')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCategory')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCategory')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 貸付種類設定ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanCategory')
                ->seePageIs('/admin/super_user/config/Suisin/LoanCategory')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //漁業形態 
    /**
     * @tests
     */
    public function 漁業形態を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Category::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFishery')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFishery')

        ;
    }

    /**
     * @tests
     */
    public function 漁業形態でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Fishery::truncate();
        $file_name = '漁業形態.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFishery')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFishery')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFishery/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFishery')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\Fishery::
                            where('fishery_form_code', trim($data[0]))->
                            where('fishery_form_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 漁業形態で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Fishery::truncate();
        $file_name = '漁業形態.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFishery')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFishery')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFishery')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 漁業形態で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Fishery::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFishery')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFishery')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFishery')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 漁業形態設定ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanFishery')
                ->seePageIs('/admin/super_user/config/Suisin/LoanFishery')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //資金区分 
    /**
     * @tests
     */
    public function 資金区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Fund::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFund')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFund')

        ;
    }

    /**
     * @tests
     */
    public function 資金区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Fund::truncate();
        $file_name = '資金区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFund')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFund')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFund/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFund')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\Fund::
                            where('fund_code', trim($data[0]))->
                            where('fund_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 資金区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Fund::truncate();
        $file_name = '資金区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFund')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFund')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFund')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 資金区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Fund::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFund')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFund')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFund')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 資金区分設定ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanFund')
                ->seePageIs('/admin/super_user/config/Suisin/LoanFund')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //資金使途区分 
    /**
     * @tests
     */
    public function 資金使途区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Fund::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsageCode')

        ;
    }

    /**
     * @tests
     */
    public function 資金使途区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundUsageCode::truncate();
        $file_name = '資金使途区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsageCode/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\FundUsageCode::
                            where('fund_usage_code', trim($data[0]))->
                            where('fund_usage_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 資金使途区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundUsageCode::truncate();
        $file_name = '資金使途区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 資金使途区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundUsageCode::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 資金使途区分ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanFundUsageCode')
                ->seePageIs('/admin/super_user/config/Suisin/LoanFundUsageCode')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //資金補助区分 
    /**
     * @tests
     */
    public function 資金補助区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundAuxiliary::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundAuxiliary')

        ;
    }

    /**
     * @tests
     */
    public function 資金補助区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundAuxiliary::truncate();
        $file_name = '資金補助区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundAuxiliary/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\FundAuxiliary::
                            where('fund_auxiliary_code', trim($data[0]))->
                            where('fund_auxiliary_category', trim($data[1]))->
                            where('fund_auxiliary_name', trim($data[2]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 資金補助区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundAuxiliary::truncate();
        $file_name = '資金補助区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 資金補助区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundAuxiliary::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 資金補助区分ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanFundAuxiliary')
                ->seePageIs('/admin/super_user/config/Suisin/LoanFundAuxiliary')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //資金用途 
    /**
     * @tests
     */
    public function 資金用途を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundUsage::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundUsage')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsage')

        ;
    }

    /**
     * @tests
     */
    public function 資金用途でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundUsage::truncate();
        $file_name = '資金用途.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundUsage')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsage')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsage/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsage')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\FundUsage::
                            where('fund_usage', trim($data[0]))->
                            where('fund_usage_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 資金用途で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundUsage::truncate();
        $file_name = '資金用途.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundUsage')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsage')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsage')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 資金用途区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\FundUsage::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanFundUsage')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsage')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanFundUsage')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 資金用途区分ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanFundUsage')
                ->seePageIs('/admin/super_user/config/Suisin/LoanFundUsage')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //自振区分 
    /**
     * @tests
     */
    public function 自振区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\JifuriCode::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->seePageIs('/admin/suisin/config/Suisin/LoanJifuriCode')

        ;
    }

    /**
     * @tests
     */
    public function 自振区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\JifuriCode::truncate();
        $file_name = '自振区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->seePageIs('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanJifuriCode/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\JifuriCode::
                            where('jifuri_code', trim($data[0]))->
                            where('jifuri_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 自振区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\JifuriCode::truncate();
        $file_name = '自振区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->seePageIs('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 自振区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\JifuriCode::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->seePageIs('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 自振区分ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanJifuriCode')
                ->seePageIs('/admin/super_user/config/Suisin/LoanJifuriCode')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //段階金利制区分 
    /**
     * @tests
     */
    public function 段階金利制区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\PhasedMoneyRate::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->seePageIs('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')

        ;
    }

    /**
     * @tests
     */
    public function 段階金利制区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\PhasedMoneyRate::truncate();
        $file_name = '段階金利制区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->seePageIs('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanPhasedMoneyRate/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\PhasedMoneyRate::
                            where('phased_money_rate_code', trim($data[0]))->
                            where('phased_money_rate_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 段階金利制区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\PhasedMoneyRate::truncate();
        $file_name = '段階金利制区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->seePageIs('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 段階金利制区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\PhasedMoneyRate::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->seePageIs('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 段階金利制区分ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanPhasedMoneyRate')
                ->seePageIs('/admin/super_user/config/Suisin/LoanPhasedMoneyRate')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //担保コード 
    /**
     * @tests
     */
    public function 担保コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Collateral::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanCollateral')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCollateral')

        ;
    }

    /**
     * @tests
     */
    public function 担保コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Collateral::truncate();
        $file_name = '担保コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanCollateral')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCollateral')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCollateral/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCollateral')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\Collateral::
                            where('collateral_code', trim($data[0]))->
                            where('collateral_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 担保コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Collateral::truncate();
        $file_name = '担保コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanCollateral')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCollateral')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCollateral')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 担保コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Collateral::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanCollateral')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCollateral')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanCollateral')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 担保コードファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanCollateral')
                ->seePageIs('/admin/super_user/config/Suisin/LoanCollateral')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //保証機関コード 
    /**
     * @tests
     */
    public function 保証機関コードを表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SecurityInstitution::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSecurityInstitution')

        ;
    }

    /**
     * @tests
     */
    public function 保証機関コードでCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SecurityInstitution::truncate();
        $file_name = '保証機関コード.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSecurityInstitution/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\SecurityInstitution::
                            where('security_institution_code', trim($data[0]))->
                            where('security_institution_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 保証機関コードで内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SecurityInstitution::truncate();
        $file_name = '保証機関コード.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 保証機関コードで誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SecurityInstitution::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 保証機関コードファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanSecurityInstitution')
                ->seePageIs('/admin/super_user/config/Suisin/LoanSecurityInstitution')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //利子補給助成機関区分 
    /**
     * @tests
     */
    public function 利子補給助成機関区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SubsidyInstitution::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyInstitution')

        ;
    }

    /**
     * @tests
     */
    public function 利子補給助成機関区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SubsidyInstitution::truncate();
        $file_name = '利子補給・助成機関区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyInstitution/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\SubsidyInstitution::
                            where('subsidy_institution_code', trim($data[0]))->
                            where('subsidy_institution_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 利子補給助成機関区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SubsidyInstitution::truncate();
        $file_name = '利子補給・助成機関区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 利子補給助成機関区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SecurityInstitution::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
     /**
     * @tests
     */
    public function 利子補給助成機関区分ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanSubsidyInstitution')
                ->seePageIs('/admin/super_user/config/Suisin/LoanSubsidyInstitution')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //利子補給助成区分
    /**
     * @tests
     */
    public function 利子補給助成区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Subsidy::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidy')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidy')

        ;
    }

    /**
     * @tests
     */
    public function 利子補給助成区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Subsidy::truncate();
        $file_name = '利子補給・助成区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidy')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidy')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidy/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidy')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\Subsidy::
                            where('subsidy_code', trim($data[0]))->
                            where('subsidy_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 利子補給助成区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Subsidy::truncate();
        $file_name = '利子補給・助成区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidy')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidy')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidy')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 利子補給助成区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\Subsidy::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidy')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidy')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidy')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
    /**
     * @tests
     */
    public function 利子補給助成区分ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanSubsidy')
                ->seePageIs('/admin/super_user/config/Suisin/LoanSubsidy')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }

    //貸付
    //利子補給・助成計算区分
    /**
     * @tests
     */
    public function 利子補給助成計算区分を表示できる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SubsidyCalculation::truncate();
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyCalculation')

        ;
    }

    /**
     * @tests
     */
    public function 利子補給助成計算区分でCSVファイルインポートできる() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SubsidyCalculation::truncate();
        $file_name = '利子補給・助成計算区分.csv';
        $path      = storage_path() . '/tests/csvUploadSuccessTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyCalculation/import')
                ->see('CSVデータの取り込みが完了しました。')
                ->see('現段階ではデータベースに反映されていません。引き続き更新処理を行ってください。')
                ->press('更新する')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->see('件の処理が終了しました。')
        ;
        $csv_file  = file($path);
        for ($i = 1; $i < count($csv_file); $i++) {
            $data = explode(',', $csv_file[$i]);
            $this->assertEquals(\App\Models\Loan\SubsidyCalculation::
                            where('subsidy_calculation_code', trim($data[0]))->
                            where('subsidy_calculation_name', trim($data[1]))->
                            count(), 1);
        }
    }

    /**
     * @tests
     */
    public function 利子補給助成計算区分で内容に不備のあるCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SubsidyCalculation::truncate();
        $file_name = '利子補給・助成計算区分.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->see('CSVファイルの内容に不備がありました。')
        ;
    }

    /**
     * @tests
     */
    public function 利子補給助成計算区分で誤ったCSVファイルがインポートされたときエラー() {
        $user      = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\Models\Loan\SubsidyCalculation::truncate();
        $file_name = 'どれとも異なる設定ファイル.csv';
        $path      = storage_path() . '/tests/csvUploadFailedTestFile/' . $file_name;
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->attach($path, 'csv_file')
                ->press('ImportCSV')
                ->seePageIs('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->see('警告')
                ->see('CSVファイル列数が一致しませんでした')
        ;
    }
/**
     * @tests
     */
    public function 利子補給助成計算区分ファイルがエクスポートできる() {
        $user = factory(\App\User::class)->create(['is_super_user' => '1']);
        \App\ZenonType::truncate();
        $this->actingAs($user)
                ->visit('/admin/super_user/config/Suisin/LoanSubsidyCalculation')
                ->seePageIs('/admin/super_user/config/Suisin/LoanSubsidyCalculation')
                ->see('ExportCSV')
                ->click('ExportCSV')
                ->assertResponseStatus(200)
        ;
    }
}
