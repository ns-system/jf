<style type="text/css">
tr.tr-top, tr.tr-top td { border-top: 1px solid #999 !important; }
.table-small th, .table-small th *, .table-small td, .table-small td * { font-size: 60%; padding: 2px !important; }
</style>
<a data-toggle="modal" href="#summary-table-{{ $key }}">
    <i class="glyphicon glyphicon-search"></i>
</a>

<div class="modal fade" id="summary-table-{{ $key }}" tabindex="-1">
  <div class="modal-dialog" style="width: 80%;">
    <div class="modal-content">
      <div class="modal-header bg-primary-important">
        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
        <h4 class="modal-title">{{ date('Y年n月', strtotime($key.'01')) }}</h4>
      </div>
      <div class="modal-body">

        <table class="table primary table-small">
          <thead>
            <tr class="bg-primary-important">
              <th>日程</th>
              <th>区分</th>
              <th>承認済</th>
              <th>未承認</th>
              <th>却下</th>
              <th>未入力</th>
              <th>合計</th>
            </tr>
          </thead>
          <tbody>
            @foreach($chart as $row)
            <tr class="tr-top">
              <th class="bg-primary-important va-middle" rowspan="2">{{ date('n/j', strtotime($row['entered_on'])) }}</th>
              <th class="bg-primary-important">予定</th>
              <td class="text-success">{{ number_format($row['予定承認済']) }}</td>
              <td class="text-warning">{{ number_format($row['予定未承認']) }}</td>
              <td class="text-danger">{{ number_format($row['予定却下']) }}</td>
              <td>{{ number_format($row['予定未入力']) }}</td>
              <td>{{ number_format($row['予定承認済'] + $row['予定未承認'] + $row['予定却下'] + $row['予定未入力']) }}</td>
            </tr>
            <tr>
              <th class="bg-primary-important">実績</th>
              <td class="text-success">{{ number_format($row['実績承認済']) }}</td>
              <td class="text-warning">{{ number_format($row['実績未承認']) }}</td>
              <td class="text-danger">{{ number_format($row['実績却下']) }}</td>
              <td>{{ number_format($row['実績未入力']) }}</td>
              <td>{{ number_format($row['実績承認済'] + $row['実績未承認'] + $row['実績却下'] + $row['実績未入力']) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>
