<div class="nav nav-pills nav-stacked list-group">
    <a role="presentation" class="list-group-item collapse bg-primary-important" data-toggle="collapse" href="#roster" style="color: #fff;"><span class="caret"></span> メニュー</a>
    <div class="collapse" id="roster">

        <a
            role="presentation"
            class="list-group-item collapse"
            href="/roster/app/calendar"
            style="padding-left: 30px;"
            data-toggle="tooltip"
            title="カレンダー形式で勤務表を入力できます。"
            data-placement="right">
            <small>カレンダー</small>
        </a>

        <a
            role="presentation"
            class="list-group-item collapse"
            href="/roster/app/list"
            style="border-bottom: none; padding-left: 30px;"
            data-toggle="tooltip"
            title="部署内他ユーザーの勤務状況を確認できます。"
            data-placement="right">
            <small>部署内勤務表</small>
        </a>

        <?php $obj = new \App\User(); $ctl_user = $obj->RosterUser(\Auth::user()->id); ?>


        @if($ctl_user->is_chief == true || ($ctl_user->is_proxy == true && $ctl_user->is_proxy_active == true))

        <small><a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#chief" style="border-bottom: none;"><span class="caret"></span> 責任者メニュー</a></small>
        <div class="collapse" id="chief">

            <a
                role="presentation"
                class="list-group-item collapse"
                href="/roster/chief/accept"
                style="border-bottom: none; padding-left: 30px;"
                data-toggle="tooltip"
                title="ユーザーが入力したデータの承認／却下を行います。"
                data-placement="right">
                <small>承認</small>
            </a>

            @if($ctl_user->is_chief == true)
            <a
                role="presentation"
                class="list-group-item collapse"
                href="/roster/chief/proxy"
                style="border-bottom: none; padding-left: 30px;"
                data-toggle="tooltip"
                title="不在時の代理承認者の登録／アクティブ化を行います。"
                data-placement="right">
                <small>代理人選択</small>
            </a>
            @endif
        </div>

        @endif


        <a
            role="presentation"
            class="list-group-item collapse"
            href="/auth/logout"
            style="padding-left: 30px;"
            data-toggle="tooltip"
            title="ログアウトします。"
            data-placement="right">
            <small>ログアウト</small>
        </a>
    </div>


</div>