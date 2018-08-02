<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
@if(!empty($rows) && !$rows->isEmpty())
<div class="col-md-12 well">
  <h2 class="border-bottom">入力状況</h2>
  <div class="col-md-6">
    <canvas id="plan-summary"></canvas>
  </div>
  <div class="col-md-6">
    <canvas id="actual-summary"></canvas>
  </div>
</div>
@endif

<script type="text/javascript">
  var bg  = ['rgba({{ $colors['承認'] }}, 0.4)','rgba({{ $colors['未承認'] }}, 0.4)','rgba({{ $colors['却下'] }}, 0.4)','rgba({{ $colors['未入力'] }}, 0.4)',];
  var bdr = ['rgb({{ $colors['承認'] }})','rgb({{ $colors['未承認'] }})','rgb({{ $colors['却下'] }})','rgb({{ $colors['未入力'] }})',];

  var p1 = [];
  var p2 = [];
  var p3 = [];
  var p4 = [];

  var a1 = [];
  var a2 = [];
  var a3 = [];
  var a4 = [];
  var labels = [];

  @foreach($rows as $row)
  p1.push({{ $row['予定承認済'] }});
  p2.push({{ $row['予定未承認'] }});
  p3.push({{ $row['予定却下'] }});
  p4.push({{ $row['予定未入力'] }});

  a1.push({{ $row['実績承認済'] }});
  a2.push({{ $row['実績未承認'] }});
  a3.push({{ $row['実績却下'] }});
  a4.push({{ $row['実績未入力'] }});

  labels.push('{{ $row['last_name'] }} {{$row['first_name']}}さん');
  @endforeach

  var plan    = document.getElementById('plan-summary').getContext('2d');
  @if(!empty($height))
  plan.canvas.height = {{ $height }};
  @endif
  plan.canvas.minHeight = 500;
  var planBar = new Chart(plan, {
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

  var actual    = document.getElementById('actual-summary').getContext('2d')
  @if(!empty($height))
  actual.canvas.height = {{ $height }};
  @endif
  var actualBar = new Chart(actual, {
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