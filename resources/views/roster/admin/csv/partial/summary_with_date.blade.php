
<div class="col-xs-6 colsize">
  <h4 style="border-bottom: 1px solid #ccc;">
    予定
    @include('roster.admin.csv.partial.date_table', ['key'=>$key, 'chart'=>$chart])

  </h4>
  <canvas id="plan-summary-date-{{ $key }}"></canvas>
</div>

<div class="col-xs-6 colsize">
  <h4 style="border-bottom: 1px solid #ccc;">実績</h4>
  <canvas id="actual-summary-date-{{ $key }}"></canvas>
</div>
{{-- {{ dd($chart) }} --}}

<script type="text/javascript">
  var p1 = [];
  var p2 = [];
  var p3 = [];
  var p4 = [];

  var a1 = [];
  var a2 = [];
  var a3 = [];
  var a4 = [];

  var labels = [];

  @foreach($chart as $row)
  p1.push({{ $row['予定承認済'] }})
  p2.push({{ $row['予定未承認'] }})
  p3.push({{ $row['予定却下'] }})
  p4.push({{ $row['予定未入力'] }})

  a1.push({{ $row['実績承認済'] }})
  a2.push({{ $row['実績未承認'] }})
  a3.push({{ $row['実績却下'] }})
  a4.push({{ $row['実績未入力'] }})

  labels.push('{{ date('n/j', strtotime($row['entered_on'])) }}')
  @endforeach
  var h = 150;

  let plan_date_ctx_{{ $key }} = document.getElementById('plan-summary-date-{{ $key }}').getContext("2d");
  plan_date_ctx_{{ $key }}.canvas.height = h
  var myLine_{{ $key }}        = new Chart(plan_date_ctx_{{ $key }}, {
    type : 'line',
    data : {
      labels : labels,
      datasets : [
      {
        label           : '承認済',
        borderColor     : 'rgb({{ $colors['承認'] }})',
        borderWidth     : 1,
        backgroundColor : 'rgba({{ $colors['承認']}}, 0.1)',
        data            : p1,
        lineTension     : 0,
      },
      {
        label           : '未承認',
        borderColor     : 'rgb({{ $colors['未承認'] }})',
        borderWidth     : 1,
        backgroundColor : 'rgba({{ $colors['未承認'] }}, 0.1)',
        data            : p2,
        lineTension     : 0,
      },
      {
        label           : '却下',
        borderColor     : 'rgb({{ $colors['却下'] }})',
        borderWidth     : 1,
        backgroundColor : 'rgba({{ $colors['却下']}}, 0.1)',
        data            : p3,
        lineTension     : 0,
      },
      {
        label           : '未入力',
        borderColor     : 'rgb({{ $colors['未入力'] }})',
        borderWidth     : 1,
        backgroundColor : 'rgba({{ $colors['未入力']}}, 0.1)',
        data            : p4,
        lineTension     : 0,
      },
      ],
    },
  });

  let actual_date_ctx_{{ $key }} = document.getElementById('actual-summary-date-{{ $key }}').getContext("2d");
  actual_date_ctx_{{ $key }}.canvas.height = h
  var myLine_{{ $key }}        = new Chart(actual_date_ctx_{{ $key }}, {
    type : 'line',
    data : {
      labels : labels,
      datasets : [
      {
        label           : '承認済',
        borderColor     : 'rgb({{ $colors['承認'] }})',
        borderWidth     : 1,
        backgroundColor : 'rgba({{ $colors['承認']}}, 0.1)',
        data            : a1,
        lineTension     : 0,
      },
      {
        label           : '未承認',
        borderColor     : 'rgb({{ $colors['未承認'] }})',
        borderWidth     : 1,
        backgroundColor : 'rgba({{ $colors['未承認'] }}, 0.1)',
        data            : a2,
        lineTension     : 0,
      },
      {
        label           : '却下',
        borderColor     : 'rgb({{ $colors['却下'] }})',
        borderWidth     : 1,
        backgroundColor : 'rgba({{ $colors['却下']}}, 0.1)',
        data            : a3,
        lineTension     : 0,
      },
      {
        label           : '未入力',
        borderColor     : 'rgb({{ $colors['未入力'] }})',
        borderWidth     : 1,
        backgroundColor : 'rgba({{ $colors['未入力']}}, 0.1)',
        data            : a4,
        lineTension     : 0,
      },
      ],
    },
  });

</script>