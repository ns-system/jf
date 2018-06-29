<div class="col-xs-6 colsize">
  <h4 style="border-bottom: 1px solid #ccc;">予定</h4>
  <canvas id="plan-summary-div-{{ $key }}"></canvas>
</div>

<div class="col-xs-6 colsize">
  <h4 style="border-bottom: 1px solid #ccc;">実績</h4>
  <canvas id="actual-summary-div-{{ $key }}"></canvas>
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
  p1.push({{ $row['予定承認済'] }});
  p2.push({{ $row['予定未承認'] }});
  p3.push({{ $row['予定却下'] }});
  p4.push({{ $row['予定未入力'] }});

  a1.push({{ $row['実績承認済'] }});
  a2.push({{ $row['実績未承認'] }});
  a3.push({{ $row['実績却下'] }});
  a4.push({{ $row['実績未入力'] }});

  labels.push('{{ $row['division_name'] }}');
  @endforeach

  var height = 250;

  var plan_div_ctx_{{ $key }} = document.getElementById('plan-summary-div-{{ $key }}').getContext('2d');
  plan_div_ctx_{{ $key }}.canvas.height = height;
  var myBar_{{ $key }}        = new Chart(plan_div_ctx_{{ $key }}, {
    type : 'horizontalBar',
    data : {
      labels   : labels,
      datasets : [
      {
        label           : '承認済',
        data            : p1,
        backgroundColor : 'rgba({{ $colors['承認'] }}, 0.4)',
        borderColor     : 'rgb({{ $colors['承認'] }})',
        borderWidth     : 1,
      },
      {
        label           : '未承認',
        data            : p2,
        backgroundColor : 'rgba({{ $colors['未承認'] }}, 0.4)',
        borderColor     : 'rgb({{ $colors['未承認'] }})',
        borderWidth     : 1,
      },
      {
        label           : '却下',
        data            : p3,
        backgroundColor : 'rgba({{ $colors['却下'] }}, 0.4)',
        borderColor     : 'rgb({{ $colors['却下'] }})',
        borderWidth     : 1,
      },
      {
        label           : '未入力',
        data            : p4,
        backgroundColor : 'rgba({{ $colors['未入力'] }}, 0.4)',
        borderColor     : 'rgb({{ $colors['未入力'] }})',
        borderWidth     : 1,
      },

      ],
    },
    options: {
      scales: {
        xAxes: [
        {
          stacked: true, 
          categoryPercentage:0.4
        }],
        yAxes: [{
          stacked: true
        }]
      },
    }
  });

  var actual_div_ctx_{{ $key }} = document.getElementById('actual-summary-div-{{ $key }}').getContext('2d');
  actual_div_ctx_{{ $key }}.canvas.height = height;
  var myBar_{{ $key }}          = new Chart(actual_div_ctx_{{ $key }}, {
    type : 'horizontalBar',
    data : {
      labels   : labels,
      datasets : [
      {
        label           : '承認済',
        data            : a1,
        backgroundColor : 'rgba({{ $colors['承認'] }}, 0.4)',
        borderColor     : 'rgb({{ $colors['承認'] }})',
        borderWidth     : 1,
      },
      {
        label           : '未承認',
        data            : a2,
        backgroundColor : 'rgba({{ $colors['未承認'] }}, 0.4)',
        borderColor     : 'rgb({{ $colors['未承認'] }})',
        borderWidth     : 1,
      },
      {
        label           : '却下',
        data            : a3,
        backgroundColor : 'rgba({{ $colors['却下'] }}, 0.4)',
        borderColor     : 'rgb({{ $colors['却下'] }})',
        borderWidth     : 1,
      },
      {
        label           : '未入力',
        data            : a4,
        backgroundColor : 'rgba({{ $colors['未入力'] }}, 0.4)',
        borderColor     : 'rgb({{ $colors['未入力'] }})',
        borderWidth     : 1,
      },

      ],
    },
    options: {
      scales: {
        xAxes: [
        {
          stacked: true, 
          categoryPercentage:0.4
        }],
        yAxes: [{
          stacked: true
        }]
      },
    }
  });
</script>