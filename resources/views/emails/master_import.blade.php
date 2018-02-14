マスタファイルの更新が完了しました。
更新されたデータは以下の通りです。

==============================
項目名　　　　：　{{$results['table']}}
ファイル名　　：　{{$results['file_name']}}
新規追加件数　：　{{number_format($results['counts']['insert_count'])}}件
更新件数　　　：　{{number_format($results['counts']['update_count'])}}件
==============================

----
{{date('Y年n月j日 G:i:sに送信')}}
このメールアドレスは送信専用です。
このメッセージに返信しないようお願いいたします。