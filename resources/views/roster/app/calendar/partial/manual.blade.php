
<p class="text-center">
  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#sampleModal">
    ［お読みください］入力方法を見る
  </button>
</p>

<style type="text/css">
.manual-img { width: 100%; height: auto; border: 1px solid #ddd; border-radius: 2px; }
</style>
<!-- モーダル・ダイアログ -->
<div class="modal fade" id="sampleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary-important">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">入力方法</h4>
      </div>
      <div class="modal-body">
        <h4 class="border-bottom">急な休暇などの入力方法について</h4>
        <div style="padding: 0px 20px;">
          <h5>予定側</h5>
          <img class="manual-img" src="{{ asset('/manuals/images/roster/001.png') }}">
          <p>
            急な休暇や遅刻・早退の場合、当初の予定通りに申請を行ってください。<br>
            あくまでも「当初はこの予定で勤務する予定だったこと」が分かれば問題ありません。
          </p>
          <h5>実績側（休暇の場合）</h5>
          <img class="manual-img" src="{{ asset('/manuals/images/roster/002.png') }}">
          <p>
            実勤務形態を「休暇」で申請してください。<br>
            詳細は「実休暇理由」から選択してください。<br>
            特別な理由がない限り、「実残業理由」に休暇理由を入力する必要はありません。<br>
            <b class="text-danger">休暇の場合、必ず「休暇理由」から入力するようにしてください。</b>
          </p>
          <h5>実績側（遅刻・早退の場合）</h5>
          <img class="manual-img" src="{{ asset('/manuals/images/roster/003.png') }}">
          <p>
            実勤務形態は当初の予定通り入力してください。<br>
            「実休暇理由」から遅刻もしくは早退を選択してください。<br>
            実際の労働時間を「実勤務時間」に、理由を「実残業理由」に入力してください。<br>
            <b class="text-danger">必ず休暇理由を「遅刻（もしくは早退）」にしてください。</b>
          </p>
          <h5>Tips</h5>
          <img class="manual-img" src="{{ asset('/manuals/images/roster/004.png') }}">
          <p>
            ボタンから簡単に申請することが可能です。
          </p>
        </div>
      </div>
    </div>
  </div>
</div>