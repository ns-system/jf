ユーザーの変更がありました。
==============================
編集者　　　：　{{$res['edit_user']->last_name}} {{$res['edit_user']->first_name}} さん
ユーザー名　：　{{$res['edited_user']->last_name}}{{$res['edited_user']->first_name}} さん
責任者代理　：　@if($res['result']['proxy'] == true)責任者代理@else 一般ユーザー @endif

代理機能　　：　@if($res['result']['active'] == true)有効@else 無効 @endif

==============================

----
{{date('Y年n月j日 G:i:sに送信')}}
このメールアドレスは送信専用です。
このメッセージに返信しないようお願いいたします。