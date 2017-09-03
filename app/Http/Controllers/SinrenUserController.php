<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SinrenUserController extends Controller
{

    public function showRosterRegisterUser() {
        $divisions  = \App\Division::get();
        $work_types = \App\WorkType::get();
        return view('roster.register.sinren_user', ['divisions' => $divisions, 'work_types' => $work_types]);
    }

    public function createRosterRegisterUser() {

        try {
            $input = \Input::all();

            \DB::connection('mysql_sinren')->beginTransaction();
//            $sinren_user              = \App\SinrenUser::user(\Auth::user()->id);
            $sinren_user              = \App\SinrenUser::firstOrNew(['user_id' => \Auth::user()->id]);
            $sinren_user->user_id     = \Auth::user()->id;
            $sinren_user->division_id = $input['division_id'];
            $sinren_user->save();
            \DB::connection('mysql_sinren')->commit();
        } catch (Exception $exc) {
            \DB::connection('mysql_sinren')->rollback();
            echo $exc->getTraceAsString();
        }

        try {
            \DB::connection('mysql_roster')->beginTransaction();
//            $roster_user               = \App\RosterUser::user(\Auth::user()->id);
            $roster_user               = \App\RosterUser::firstOrNew(['user_id' => \Auth::user()->id]);
            $roster_user->user_id      = \Auth::user()->id;
            $roster_user->is_chief     = $this->getCheckBox($input['is_chief']);
            $roster_user->is_proxy     = $this->getCheckBox($input['is_proxy']);
            $roster_user->work_type_id = (int) $input['work_type_id'];
            $roster_user->save();
            \DB::connection('mysql_roster')->commit();
        } catch (Exception $exc) {
            \DB::connection('mysql_roster')->rollback();
            echo $exc->getTraceAsString();
        }
//
        try {
            \DB::connection('mysql_sinren')->beginTransaction();
//            var_dump($input);
            foreach ($input['control_division_id'] as $i => $div_id) {
                $i++;
                if ((int) $div_id == 0)
                {
                    continue;
                }
                echo $div_id;
                $key                     = ['user_id' => \Auth::user()->id, 'control_number' => $i];
                $ctl_div                 = \App\ControlDivision::firstOrNew($key);
                $ctl_div->user_id        = \Auth::user()->id;
                $ctl_div->division_id   = $div_id;
                $ctl_div->control_number = $i;
                $ctl_div->save();
            }
            \DB::connection('mysql_sinren')->commit();
        } catch (Exception $exc) {
            \DB::connection('mysql_sinren')->rollback();
            echo $exc->getTraceAsString();
        }

//        var_dump($input);
        \Session::flash('flash_message', 'ユーザー登録が完了しました。');
        return \Redirect('/roster/app/home');

    }

    private function getCheckBox($buf) {
        if ($buf == '')
        {
            return 0;
        }
        return 1;
    }

}
