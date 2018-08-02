{{ $row->division_name }}
@foreach($names as $name)
{{ $name }}さん
@endforeach

入力もしくは承認されていない項目があります。
確認後、入力をお願いします。
==============================
日付 ： {{ $row->entered_on }}
職員 ： {{ $row->name }}さん
予定 ： {{ $row->plan }}
実績 ： {{ $row->actual }}

入力 ： {{ route('app::roster::calendar::show', ['ym'=>$row->month_id]) }}
承認 ： {{ route('app::roster::accept::calendar', ['ym'=>$row->month_id, 'div'=>$row->division_id]) }}
==============================

----
{{date('Y年n月j日 G:i:sに送信')}}
このメールアドレスは送信専用です。
このメッセージに返信しないようお願いいたします。