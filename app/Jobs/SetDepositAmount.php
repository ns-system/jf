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

    protected $email;

    public function __construct($email) {
        $this->email = $email;
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
                $this->updateModel('old_betsudan_account_ledgers', 'principal', new \App\Models\Deposit\OldBetsudan());
                $this->updateModel('old_betsudan_account_ledgers', 'surface_balance', new \App\Models\Deposit\OldBetsudan());
                // new_betsudan
                $this->updateModel('new_betsudan_account_ledgers', 'surface_balance', new \App\Models\Deposit\NewBetsudan());
                // chochiku
                $this->updateModel('chochiku_account_ledgers', 'surface_balance', new \App\Models\Deposit\Chochiku());
                // teiki
                $this->updateModel('teiki_account_ledgers', 'principal', new \App\Models\Deposit\Teiki());
                // tsumitei
                $this->updateModel('tsumitei_account_ledgers', '(deposit_amount + interest_amount)', new \App\Models\Deposit\Tsumitei());
                // teitsumi
                $this->updateModel('teitsumi_account_ledgers', 'payment_balance', new \App\Models\Deposit\Teitsumi());
                // tuchi
                $this->updateModel('tsuchi_account_ledgers', 'principal', new \App\Models\Deposit\Tsuchi());
                // zaikei
                $this->updateModel('zaikei_account_ledgers', 'bankbook_balance', new \App\Models\Deposit\Zaikei());
            });
            $this->sendSuccessMessage('', $this->email);
            echo "[end   : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
        } catch (\Throwable $e) {
            echo "[error : " . date('Y-m-d H:i:s') . "]" . PHP_EOL;
            $this->sendErrorMessage($e, $this->email);
            throw $e;
        }
    }

    private function updateModel($table_name, $amount_name, $model) {
//        echo "{$table_name} - {$amount_name}";

        echo "  -- processing : {$table_name}" . PHP_EOL;
//        $balance = "{$table_name}.{$amount_name}";
        $balance = $amount_name;
        $params  = [
            "{$table_name}.common_id",
            'common_account_ledgers.customer_number',
            'common_account_ledgers.subject_code',
            'common_account_ledgers.account_number',
            'common_account_ledgers.key_account_number',
            'common_account_ledgers.contract_number',
            'common_account_ledgers.monthly_id',
//            "{$balance} as balance",
        ];

        $chunk = floor(65000 / (count($params) + 6));

        $where = ['common_account_ledgers.id', '>=', 0];
        switch ($table_name) {
            case 'tsuchi_account_ledgers':
                $sql   = $this->getPartial($balance, $table_name, 'tr_record_state');
                break;
            case 'teiki_account_ledgers':
                $sql   = $this->getTeiki($balance, $table_name, 'contract_tr_record_state');
                break;
            case 'old_betsudan_account_ledgers':
                $sql   = $this->getPartial($balance, $table_name, 'contract_tr_record_state');
                $where = ($balance == 'principal') ? ['common_account_ledgers.category_code', '<=', 99] : ['common_account_ledgers.category_code', '>=', 100];
                break;
            default:
                $sql   = $this->getCommon($balance);
                break;
        }
//        var_dump($where);
//        $s = 
        $model->join('zenon_db.common_account_ledgers', 'common_account_ledgers.id', '=', "{$table_name}.common_id")
                ->select($params)
                ->addSelect(\DB::raw("{$balance} AS balance"))
                ->addSelect(\DB::raw("CASE WHEN {$balance} >= 0 THEN true ELSE false END AS is_plus"))
                ->addSelect(\DB::raw($sql))
                ->where($where[0], $where[1], $where[2])
                ->chunk($chunk, function($records) {
                    \App\DepositAmount::insert($records->toArray());
                })
//                ->toSql()
        ;
//        var_dump($s);
    }

    private function getCommon($balance) {
        $sql = "CASE WHEN ({$balance} >= 0 AND common_account_ledgers.record_state_1 = 0 AND common_account_ledgers.record_state_2 = 0) THEN true ELSE false END AS is_aggregate";
        return "common_account_ledgers.record_state_2 AS tr_state_1 , common_account_ledgers.record_state_2 AS tr_state_2 , {$sql}";
//        return [
//            'common_account_ledgers.record_state_2 AS tr_state_1',
//            'common_account_ledgers.record_state_2 AS tr_state_2',
//            $sql
//        ];
//        $model->addSelect(['common_account_ledgers.record_state_2 AS tr_state_1', 'common_account_ledgers.record_state_2 AS tr_state_2', $sql]);
//        return $model;
    }

    private function getPartial($balance, $table_name, $column_name) {
        $sql = "CASE WHEN ({$balance} >= 0 AND common_account_ledgers.record_state_1 = 0 AND common_account_ledgers.record_state_2 = 0 AND {$balance} >= 0 AND {$table_name}.{$column_name}_1 = 0 AND {$table_name}.{$column_name}_2 = 0) THEN true ELSE false END AS is_aggregate";
        return "{$table_name}.{$column_name}_1 AS tr_state_1, {$table_name}.{$column_name}_2 AS tr_state_2, {$sql}";
    }

    private function getTeiki($balance, $table_name, $column_name) {
        $sql = "CASE WHEN ({$balance} >= 0 AND common_account_ledgers.record_state_1 = 0 AND common_account_ledgers.record_state_2 = 0 AND {$balance} >= 0 AND ({$table_name}.filioparental_state = 1 OR {$table_name}.filioparental_state = 2) AND {$table_name}.{$column_name}_1 = 0) THEN true ELSE false END AS is_aggregate";
        return "{$table_name}.filioparental_state, {$table_name}.{$column_name}_1 AS tr_state_1, {$table_name}.{$column_name}_2 AS tr_state_2, {$sql}";
    }

//    tuchi
//    tr_record_state
//    old_betsu
//    contract_tr_record_state
//    teiki
//    contract_tr_record_state

    public function failed() {
        
    }

}
