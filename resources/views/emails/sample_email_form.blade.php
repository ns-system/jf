<h1>サンプルメール送信フォーム</h1>



<form method="POST" action="/email/send">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <table>
        <tbody>
            <tr>
                <th width="200px">DRIVER</th>
                <td width="600px;">{{env('MAIL_DRIVER')}}</td>
            </tr>
            <tr>
                <th>HOST</th>
                <td>{{env('MAIL_HOST')}}</td>
            </tr>
            <tr>
                <th>PORT</th>
                <td>{{env('MAIL_PORT')}}</td>
            </tr>
            <tr>
                <th>FROM ADDRESS</th>
                <td>{{env('MAIL_FROM_ADDRESS')}}</td>
            </tr>
            <tr>
                <th>USER NAME</th>
                <td>{{env('MAIL_USERNAME')}}</td>
            </tr>
            <tr>
                <th>TO ADDRESS</th>
                <td>
                    <input type="text" name="addr" value="@jf-nssinren.or.jp" style="width: 300px">
                    <button type="submit">メール送信</button>

                </td>
            </tr>
        </tbody>
    </table>
</form>