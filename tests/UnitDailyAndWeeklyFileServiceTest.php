<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UnitUserServicesTest
 *
 * @author r-kawanishi
 */
use App\Services\Traits\Testing\FileTestable;

class UnitUserServicesTest extends TestCase
{

    use FileTestable;

    /**
     * @tests
     */
    public function 異常系_ユーザーアイコン変更時にエラーが起きる() {
        $service = new \App\Services\DailyAndWeeklyFileService();
        $service->getFileList('daily', '201711');
    }

}
