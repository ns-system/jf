<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConfigPageTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testVisitPage() {
        $user = \App\User::where('email', '=', 'sample@sample.com')->first();
//        var_dump($user);
        $this->actingAs($user)
                ->visit('/admin/suisin/config/Suisin/Consignor')
                ->visit('/admin/suisin/config/Suisin/ConsignorGroup')
                ->visit('/admin/suisin/config/Suisin/Prefecture')
                ->visit('/admin/suisin/config/Suisin/Store')
                ->visit('/admin/suisin/config/Suisin/SmallStore')
                ->visit('/admin/suisin/config/Suisin/Area')
                ->visit('/admin/suisin/config/Suisin/ControlStore')
                ->visit('/admin/suisin/config/Suisin/Subject')
                ->visit('/admin/suisin/config/Suisin/Industry')
                ->visit('/admin/suisin/config/Suisin/Qualification')
                ->visit('/admin/suisin/config/Suisin/Personality')
                
                ->visit('/admin/suisin/config/Suisin/DepositCategory')
                ->visit('/admin/suisin/config/Suisin/DepositBankbookType')
                ->visit('/admin/suisin/config/Suisin/DepositGist')
                
                ->visit('/admin/suisin/config/Suisin/DepositTaxation')
                ->visit('/admin/suisin/config/Suisin/DepositTerm')
                ->visit('/admin/suisin/config/Suisin/DepositContinuation')
                
                ->visit('/admin/suisin/config/Suisin/LoanCategory')
                ->visit('/admin/suisin/config/Suisin/LoanCollateral')
                ->visit('/admin/suisin/config/Suisin/LoanFishery')
                ->visit('/admin/suisin/config/Suisin/LoanFund')
                ->visit('/admin/suisin/config/Suisin/LoanFundAuxiliary')
                ->visit('/admin/suisin/config/Suisin/LoanFundUsage')
                ->visit('/admin/suisin/config/Suisin/LoanFundUsageCode')
                ->visit('/admin/suisin/config/Suisin/LoanJifuriCode')
                ->visit('/admin/suisin/config/Suisin/LoanPhasedMoneyRate')
                ->visit('/admin/suisin/config/Suisin/LoanSecurityInstitution')
                ->visit('/admin/suisin/config/Suisin/LoanSubsidy')
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyCalculation')
                ->visit('/admin/suisin/config/Suisin/LoanSubsidyInstitution')
                
                
        ;
    }

}
