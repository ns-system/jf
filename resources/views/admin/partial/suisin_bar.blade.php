<a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#consignor"><span class="caret"></span> <small>委託者情報</small></a>
<div class="collapse" id="consignor">

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'Consignor'])}}"
        data-toggle="tooltip"
        title="委託者名・委託者の所属するグループの変更が行えます。"
        data-placement="right">委託者リスト</a>

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'ConsignorGroup'])}}"
        data-toggle="tooltip"
        title="委託者グループの変更が行えます。"
        data-placement="right">委託者グループ</a>
    <span class="list-group-item collapse list-divider"></span>
</div>
<a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#area"><span class="caret"></span> <small>エリア情報</small></a>
<div class="collapse" id="area">

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'Prefecture'])}}"
        data-toggle="tooltip"
        title="県コードの変更が行えます。"
        data-placement="right">県コード</a>

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'Store'])}}"
        data-toggle="tooltip"
        title="支店番号の変更が行えます。"
        data-placement="right">店番</a>

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'SmallStore'])}}"
        data-toggle="tooltip"
        title="小規模店番の変更が行えます。"
        data-placement="right">小規模店番</a>

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'Area'])}}"
        data-toggle="tooltip"
        title="地区コードの変更が行えます。"
        data-placement="right">地区コード</a>

    <a
        role="presentation"
        class="list-group-item collapse list-second"
        href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'ControlStore'])}}"
        data-toggle="tooltip"
        title="管轄店舗（小規模店番を管轄する支店）の変更が行えます。"
        data-placement="right">管轄店舗</a>

    <span class="list-group-item collapse list-divider"></span>
</div>

<a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#master"><span class="caret"></span> <small>マスタファイル</small></a>
<div class="collapse" id="master">

    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#common"><span class="caret"></span> <small>共通</small></a>
    <div class="collapse" id="common">
        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'Subject'])}}"
            data-toggle="tooltip"
            title="科目コードの編集が行えます。"
            data-placement="right">科目コード</a>
        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'Industry'])}}"
            data-toggle="tooltip"
            title="業種コードの編集が行えます。"
            data-placement="right">業種コード</a>

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'Qualification'])}}"
            data-toggle="tooltip"
            title="資格区分の編集が行えます。"
            data-placement="right">資格区分</a>
        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'Personality'])}}"
            data-toggle="tooltip"
            title="人格コードの編集が行えます。"
            data-placement="right">人格コード</a>

        <span class="list-group-item collapse list-divider"></span>
    </div>

    <a role="presentation" class="list-group-item collapse" data-toggle="collapse" href="#deposit"><span class="caret"></span> <small>貯金</small></a>
    <div class="collapse" id="deposit">
        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'DepositCategory'])}}"
            data-toggle="tooltip"
            title="種類コードの編集が行えます。"
            data-placement="right">種類コード</a>

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'DepositBankbookType'])}}"
            data-toggle="tooltip"
            title="通証タイプの編集が行えます。"
            data-placement="right">通証タイプ</a>

        <!--{{--             <a
                role="presentation"
                class="list-group-item collapse list-second"
                href="/admin/suisin/Suisin/DepositBankbookCode"
                data-toggle="tooltip"
                title="通証区分の編集が行えます。"
                data-placement="right">通証区分</a        > --}}-->

        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="{{route('admin::suisin::index', ['system'=>'Suisin', 'category'=>'DepositGist'])}}"
            data-toggle="tooltip"
            title="摘要コード・ビジネスネット経済摘要の編集が行えます。"
            data-placement="right">摘要コード</a>

        <!--
        <a
            role="presentation"
            class="list-group-item collapse list-second"
            href="/admin/suisin/Suisin/DepositAuxiliary"
            data-toggle="tooltip"
            title="補助コードの編集が行えます。"
            data-placement="right">補助コード</a>
        -->
        <span class="list-group-item collapse list-divider"></span>
    </div>
</div>
