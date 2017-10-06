<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Http\Requests\Nikocale\Nikocale;
use App\Http\Controllers\Controller;

class NikocaleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getEmotion($monthly_id) {
        return \App\Emotion::join('sinren_db.sinren_users', 'user_emotions.user_id', '=', 'sinren_users.user_id')
                        ->join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
                        ->join('laravel_db.users', 'user_emotions.user_id', '=', 'users.id')
                        ->where('entered_on', '>=', date('Y-m-d', strtotime($monthly_id . '01')))
                        ->where('entered_on', '<=', date('Y-m-t', strtotime($monthly_id . '01')))
                        ->orderBy('user_emotions.user_id', 'asc')
        ;
    }

    public function index($para_monthly_id = null) {
        $monthly_id       = (empty($para_monthly_id)) ? date('Ym') : $para_monthly_id;
        $calendar_service = new \App\Services\Roster\Calendar();
        $tmp_calendar     = $calendar_service->setId($monthly_id)->makeCalendar();
        $calendar         = $calendar_service->convertCalendarToList($tmp_calendar);
//        dd($calendar);
        // 部署の特定
        $division         = \App\SinrenUser::join('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')->where('user_id', '=', \Auth::user()->id)->first();
//        // 部署＋日付から部署内感情リストを取得
//        $users            = \App\SinrenUser::where('division_id', '=', $division->division_id)->get();
        // リストをキー化 - 日付も選択肢に入れてね
        $tmp_emotions     = $this->getEmotion($monthly_id)
                ->where('sinren_divisions.division_id', '=', $division->division_id)
                ->select(\DB::raw('*, user_emotions.id AS key_id'))
                ->get()
        ;

        $emotions = [];
        $user_ids = [];
        // キー変換
        foreach ($tmp_emotions as $emo) {
            $emotions[$emo->user_id][$emo->entered_on] = $emo;
        }

        // ユーザーID取得
        $users = \App\SinrenUser::where('division_id', '=', $division->division_id)->groupBy('user_id')->get(['user_id'])->toArray();
        foreach ($users as $u) {
            $user_ids[] = $u['user_id'];
        }

        // カウント
        $user_id     = \Auth::user()->id;
        $me          = $this->getEmotion($monthly_id)->where('user_emotions.user_id', '=', $user_id)->groupBy('emotion')->select(\DB::raw('emotion, COUNT(*) AS total'))->get()->toArray();
        $other       = $this->getEmotion($monthly_id)->where('user_emotions.user_id', '<>', $user_id)->where('sinren_users.division_id', '=', $division->division_id)->groupBy('emotion')->select(\DB::raw('emotion, COUNT(*) AS total'))->get()->toArray();
        $my_count    = [1 => 0, 2 => 0, 3 => 0];
        $other_count = [1 => 0, 2 => 0, 3 => 0];
        foreach ($me as $m) {
            $my_count[$m['emotion']] = $m['total'];
        }
        foreach ($other as $o) {
            $other_count[$o['emotion']] = $o['total'];
        }
        return view('nikocale.app.index', ['calendar' => $calendar, 'emotions' => $emotions, 'user_ids' => $user_ids, 'my_count' => $my_count, 'other_count' => $other_count, 'monthly_id' => $monthly_id, 'division' => $division]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($user_id, $entered_on, Nikocale $request) {
//        dd($entered_on);
        $input            = $request->only(['emotion', 'comment']);
        $emotion          = \App\Emotion::firstOrCreate(['user_id' => $user_id, 'entered_on' => $entered_on]);
        $emotion->emotion = $input['emotion'];
        $emotion->comment = $input['comment'];
        $emotion->save();
//        var_dump($emotion);
        \Session::flash('success_message', "データが更新されました。");
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        \App\Emotion::find($id)->delete();
        \Session::flash('info_message', "データが削除されました。");
        return back();
    }

}
