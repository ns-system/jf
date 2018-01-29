<?php

namespace App\Services\Traits;

trait ErrorMailSendable
{

    public function sendErrorMessage($error, $email) {
        try {
            \Mail::send('emails.error', ['error' => $error], function($message) use($email) {
                $message->to($email)
                        ->subject("処理中にエラーが発生しました")
                ;
            });
        } catch (\Exception $exc) {
            var_dump($exc->getMessage());
        }
    }

}
