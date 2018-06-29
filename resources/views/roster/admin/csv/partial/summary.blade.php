<div class="col-xs-6 colsize">
  <h4 style="border-bottom: 1px solid #ccc;">予定</h4>
  <canvas id="plan-summary-{{ $key }}"></canvas>
</div>

<div class="col-xs-6 colsize">
  <h4 style="border-bottom: 1px solid #ccc;">実績</h4>
  <canvas id="actual-summary-{{ $key }}"></canvas>
</div>
{{-- {{ dd($chart) }} --}}

<script type="text/javascript">

  var bg  = ['rgba({{ $colors['承認'] }}, 0.4)','rgba({{ $colors['未承認'] }}, 0.4)','rgba({{ $colors['却下'] }}, 0.4)','rgba({{ $colors['未入力'] }}, 0.4)',];
  var bdr = ['rgb({{ $colors['承認'] }})','rgb({{ $colors['未承認'] }})','rgb({{ $colors['却下'] }})','rgb({{ $colors['未入力'] }})',];
  var height = 150;


  let plan_ctx_{{ $key }}      = document.getElementById('plan-summary-{{ $key }}').getContext("2d");
  plan_ctx_{{ $key }}.canvas.height = height

  var myDoughnut_{{ $key }} = new Chart(plan_ctx_{{ $key }}, {
    type : 'pie',
    data : {
      labels : ['承認済','未承認','却下','未入力'],
      datasets : [
      {
        borderColor     : bdr,
        backgroundColor : bg,
        borderWidth     : 1,
        data            : [{{ $chart['予定承認済'] }},{{ $chart['予定未承認'] }},{{ $chart['予定却下'] }},{{ $chart['予定未入力'] }}],
      },
      ],
    },
  });

  let actual_ctx_{{ $key }}    = document.getElementById('actual-summary-{{ $key }}').getContext("2d");
  actual_ctx_{{ $key }}.canvas.height = height

  var myDoughnut_{{ $key }} = new Chart(actual_ctx_{{ $key }}, {
    type : 'pie',
    data : {
      labels : ['承認済','未承認','却下','未入力'],
      datasets : [
      {
        borderColor     : bdr,
        backgroundColor : bg,
        borderWidth     : 1,
        data            : [{{ $chart['実績承認済'] }},{{ $chart['実績未承認'] }},{{ $chart['実績却下'] }},{{ $chart['実績未入力'] }}],
      },
      ],
    },
  });

</script>