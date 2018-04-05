<?php

namespace App\Services\Traits;

trait ErrorMailSendable
{

    public function sendErrorMessage($error, $email) {
        try {
            \Mail::send('emails.error', ['error' => $error], function($message) use($email) {
                $message->to($email)
                        ->subject("[" . date('Y-m-d H:i:s') . "] 処理中にエラーが発生しました")
                ;
            });
            echo "==== ErrorLog =========================" . PHP_EOL;
            echo "TIMESTAMP: " . date("Y-m-d H:i:s") . PHP_EOL;
            echo "FILE     : " . $error->getFile() . PHP_EOL;
            echo "LINE     : " . $error->getLine() . PHP_EOL;
            echo "MESSAGE  : " . $error->getMessage() . PHP_EOL;
            echo "=======================================" . PHP_EOL;
        } catch (\Throwable $exc) {
            var_dump($exc->getMessage());
        }
    }

}
