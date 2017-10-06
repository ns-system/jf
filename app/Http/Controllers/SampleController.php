<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SampleController extends Controller
{

    private function setGroupCount($consignor) {
        $groups = \App\ConsignorGroup::orderBy('id')->get(['id', 'group_name']);
//        var_dump($consignor);
        $tmp    = [];
        foreach ($groups as $i => $grp) {
            $tmp[$i] = 0;
        }
        foreach ($consignor as $cns) {
//            var_dump($cns);
            foreach ($groups as $i => $grp) {
                if ($cns->Consignor->consignor_group_id == $grp->id)
                {
                    $tmp[$i] ++;
                }
            }
        }
        return $tmp;
    }

    public function exportSample() {

        $control_store_code = (int) \Request::input('control', 0);
        $small_store_number = (int) \Request::input('small', 0);

        if ($small_store_number != 0)
        {
            $store_where = ['jifuri_trading_files.small_store_number' => $small_store_number];
        }
        elseif ($control_store_code != 0)
        {
            $store_where = ['control_store_code' => $control_store_code];
        }
        else
        {
            $store_where = [];
        }

        $customers = \App\Jifuri::join('master_db.small_stores', 'jifuri_trading_files.small_store_number', '=', 'small_stores.small_store_number')
                ->join('zenon_db.customer_information_files', 'customer_information_files.customer_number', '=', 'jifuri_trading_files.customer_number')
                ->select(\DB::raw('count(*) as total_count, jifuri_trading_files.customer_number, customer_information_files.kanji_name, jifuri_trading_files.small_store_number, control_store_code'))
        ;
        if ($store_where !== [null])
        {
            $customers->Where($store_where);
        }
        $customers = $customers
                ->orderBy('total_count', 'desc')
                ->orderBy('jifuri_trading_files.customer_number', 'asc')
                ->groupBy('jifuri_trading_files.customer_number')
                ->get()
        ;


        $group_headers  = \App\ConsignorGroup::get(['id', 'group_name']);
        $group_template = [];
        foreach ($group_headers as $grp) {
            $group_template[$grp->id] = 0;
        }
        
        $customer_groups = [];
        foreach ($customers as $customer) {
            $customer_no                   = $customer->customer_number;
            $tmp                           = \App\Jifuri::join('suisin_db.consignors', 'jifuri_trading_files.consignor_code', '=', 'consignors.consignor_code')
                    ->select(\DB::raw('count(*) as count, consignors.consignor_group_id'))
                    ->groupBy('consignors.consignor_group_id')
                    ->where(['jifuri_trading_files.customer_number' => $customer_no])
                    ->get()
            ;
            $sample = $group_template;
            foreach($tmp as $t){
                $sample[$t->consignor_group_id] = $t->count;
            }
//            var_dump($sample);
            $customer_groups[$customer_no] = $sample;
        }
//        var_dump($customer_groups);

        $titles = [
            0 => '顧客番号',
            1 => '氏名',
        ];

        $i = 2;
        foreach ($group_headers as $grp_head) {
            $titles[$i] = $grp_head->group_name;
//            $titles[$i] = $i;
            $i++;
        }
        $titles[$i] = '合計';
//        var_dump($titles);
        
        
        $export_datas = [];
        foreach($customers as $cst){
            $tmp[0] = $cst->customer_number;
            $tmp[1] = $cst->kanji_name;
            $i = 2;
            foreach($customer_groups[$cst->customer_number] as $k=>$grp){
//                var_dump($k.' - '.$grp);
                $tmp[$i] = $grp;
                $i++;
            }
            $tmp[$i] = $cst->total_count;
//            var_dump($tmp->toArray());
            $export_datas[] = $tmp->toArray();
        }
//        var_dump($export_datas);
//        exit();



        $obj = new \App\Providers\CsvServiceProvider();
        return $obj->exportCsv($export_datas, '自振契約状況_' . date('Ymd_His') . '.csv', $titles);
    }

    public function showSample() {
        $control_store_code = (int) \Request::input('control', 0);
        $small_store_number = (int) \Request::input('small', 0);

        if ($small_store_number != 0)
        {
            $store_where = ['jifuri_trading_files.small_store_number' => $small_store_number];
        }
        elseif ($control_store_code != 0)
        {
            $store_where = ['control_store_code' => $control_store_code];
        }
        else
        {
            $store_where = [];
        }


        $customers = \App\Jifuri::join('master_db.small_stores', 'jifuri_trading_files.small_store_number', '=', 'small_stores.small_store_number')
                ->join('zenon_db.customer_information_files', 'customer_information_files.customer_number', '=', 'jifuri_trading_files.customer_number')
                ->select(\DB::raw('count(*) as total_count, jifuri_trading_files.customer_number, customer_information_files.kanji_name, jifuri_trading_files.small_store_number, control_store_code'))
        ;
        if ($store_where !== [null])
        {
            $customers->Where($store_where);
        }
        $customers = $customers
                ->orderBy('total_count', 'desc')
                ->orderBy('jifuri_trading_files.customer_number', 'asc')
                ->groupBy('jifuri_trading_files.customer_number')
                ->paginate(50)
        ;



        $customer_groups = [];
        foreach ($customers as $customer) {
            $customer_no                   = $customer->customer_number;
            $tmp                           = \App\Jifuri::join('suisin_db.consignors', 'jifuri_trading_files.consignor_code', '=', 'consignors.consignor_code')
                    ->select(\DB::raw('count(*) as count, consignors.consignor_group_id'))
                    ->groupBy('consignors.consignor_group_id')
                    ->where(['jifuri_trading_files.customer_number' => $customer_no])
                    ->get()
            ;
            $customer_groups[$customer_no] = $tmp;
        }

        $group_headers = \App\ConsignorGroup::get(['id', 'group_name']);


        $control_store = \App\ControlStore::get(['control_store_code', 'control_store_name']);
        $small_store   = \App\SmallStore::get(['small_store_number', 'control_store_code', 'small_store_name']);

        $vals = [
            'customers'       => $customers,
            'customer_groups' => $customer_groups,
            'group_headers'   => $group_headers,
            'controls'        => $control_store,
            'smalls'          => $small_store,
        ];

        return view('app.sample', $vals)
                        ->with([
                            'control' => $control_store_code,
                            'small'   => $small_store_number,
                        ])
        ;

    }

}
