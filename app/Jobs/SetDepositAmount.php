<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Traits\ErrorMailSendable;

class SetDepositAmount extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        ErrorMailSendable
    ;

    public function __construct() {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        try {
            echo "==== setDepositAmount ====" . PHP_EOL;
            echo "[start : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
            $amounts = new \App\DepositAmount();
            $amounts->truncate();

            \DB::connection('mysql_zenon')->transaction(function() {
                // futsu
                $this->updateModel('futsu_account_ledgers', 'surface_balance', new \App\Models\Deposit\Futsu());
                // toza
                $this->updateModel('toza_account_ledgers', 'surface_balance', new \App\Models\Deposit\Toza());
                // old_betsudan
                $this->updateModel('old_betsudan_account_ledgers', 'surface_balance', new \App\Models\Deposit\OldBetsudan());
                // new_betsudan
                $this->updateModel('new_betsudan_account_ledgers', 'surface_balance', new \App\Models\Deposit\NewBetsudan());
                // chochiku
                $this->updateModel('chochiku_account_ledgers', 'surface_balance', new \App\Models\Deposit\Chochiku());
                // teiki
                $this->updateModel('teiki_account_ledgers', 'principal', new \App\Models\Deposit\Teiki());
                // tsumitei
                $this->updateModel('tsumitei_account_ledgers', 'deposit_amount', new \App\Models\Deposit\Tsumitei());
                // teitsumi
                $this->updateModel('teitsumi_account_ledgers', 'payment_balance', new \App\Models\Deposit\Teitsumi());
                // tuchi
                $this->updateModel('tsuchi_account_ledgers', 'principal', new \App\Models\Deposit\Tsuchi());
                // zaikei
                $this->updateModel('zaikei_account_ledgers', 'bankbook_balance', new \App\Models\Deposit\Zaikei());
            });
            echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
        } catch (\Throwable $e) {
            echo "[error : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
            $this->sendErrorMessage($e, 'n.teshima@jf-nssinren.or.jp');
            throw $e;
        }
    }

    private function updateModel($table_name, $amount_name, $model) {
//        echo "{$table_name} - {$amount_name}";

        echo "  -- processing : {$table_name}" . PHP_EOL;
        $params = [
            "{$table_name}.common_id",
            'common_account_ledgers.customer_number',
            'common_account_ledgers.subject_code',
            'common_account_ledgers.account_number',
            'common_account_ledgers.key_account_number',
            'common_account_ledgers.contract_number',
            'common_account_ledgers.monthly_id',
            "{$table_name}.{$amount_name} as balance",
        ];

        $chunk = floor(65000 / count($params));

//        \DB::connection('mysql_zenon')->transaction(function() use($model, $table_name, $params, $chunk) {
        $model->join('zenon_db.common_account_ledgers', 'common_account_ledgers.id', '=', "{$table_name}.common_id")
                ->select($params)
                ->chunk($chunk, function($records) {
//                    $amounts->insert($records->toArray());
                    \App\DepositAmount::insert($records->toArray());
                })
        ;
//        });
    }

    public function failed() {
        
    }

}
