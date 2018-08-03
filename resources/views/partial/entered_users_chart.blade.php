<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<style type="text/css">
.chart-wrap { position: relative; }
.disabled   { opacity: 0.3; }
.progress   { position: absolute; width: 100%; z-index: 1; }
</style>
<div class="col-md-12 well">
  <h2 class="border-bottom">
    <b class="text-success pointer" onclick="editMonth(-1)"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></b>
    入力状況 - <small id="month"></small>
    <b class="text-success pointer" onclick="editMonth(+1)"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></b>
  </h2>
  <div class="chart-wrap">
    <div class="progress" style="display: none;"><div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" style="width: 100%;"></div></div>
    <div class="col-md-6">
      <canvas id="plan-summary"></canvas>
    </div>
    <div class="col-md-6">
      <canvas id="actual-summary"></canvas>
    </div>
  </div>
</div>

<script type="text/javascript">
  var bg  = ['rgba({{ $colors['承認'] }}, 0.4)','rgba({{ $colors['未承認'] }}, 0.4)','rgba({{ $colors['却下'] }}, 0.4)','rgba({{ $colors['未入力'] }}, 0.4)',]
  var bdr = ['rgb({{ $colors['承認'] }})','rgb({{ $colors['未承認'] }})','rgb({{ $colors['却下'] }})','rgb({{ $colors['未入力'] }})',]
  var p1 = []
  var p2 = []
  var p3 = []
  var p4 = []
  var a1 = []
  var a2 = []
  var a3 = []
  var a4 = []
  var labels   = []
  var month    = new Date()
  var loading  = false
  var plan     = null
  var actual   = null
  var dataset  = {
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
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        xAxes: [
        {
          stacked: true,
          categoryPercentage:0.4,
        }],
        yAxes: [{
          stacked: true,
        }]
      },
    }
  }


  function editMonth (operand) {
    lastMonth = month.setMonth(month.getMonth() + operand)
    month = new Date(lastMonth)
    $('#month').html(getMonth("YYYY年MM月分"))
    getChart()
  }

  function getMonth (format) {
    let m = ('0' + (month.getMonth() + 1)).slice(-2)
    return format.replace('YYYY', month.getFullYear()).replace('MM', m)
  }

  function edit () {
    console.log('edit',plan, plan.data)
    plan.data.labels = labels
    plan.data.datasets[0].data = p1
    plan.data.datasets[1].data = p2
    plan.data.datasets[2].data = p3
    plan.data.datasets[3].data = p4
    plan.update()
    actual.data.labels = labels
    actual.data.datasets[0].data = a1
    actual.data.datasets[1].data = a2
    actual.data.datasets[2].data = a3
    actual.data.datasets[3].data = a4
    actual.update()
  }

  function getChart () {
    let month_id = getMonth('YYYYMM')
    console.log(month_id)
    $('.chart-wrap').addClass('disabled')
    $('.progress').show()
    let params = { url : '/home/chart', type : 'GET', data : { month_id : month_id } }
    $.ajax(params)
    .done((res) => {
      p1 = res.p1
      p2 = res.p2
      p3 = res.p3
      p4 = res.p4
      a1 = res.a1
      a2 = res.a2
      a3 = res.a3
      a4 = res.a4
      labels = res.names
      drawChart()
      $('.chart-wrap').removeClass('disabled')
      $('.progress').hide()
    })
    .fail((e) => {
      $('.chart-wrap').removeClass('disabled')
      $('.progress').hide()
      alert('エラーが発生しました。')
    })
  }

  $(document).ready(function () {
    console.log('ready')
    $('#month').html(getMonth("YYYY年MM月分"))
    getChart()
  })

  function drawChart () {
    console.log('draw')
    if (plan)
      plan.destroy()
    if (actual)
      actual.destroy()
    var p   = document.getElementById('plan-summary').getContext('2d')
    var a   = document.getElementById('actual-summary').getContext('2d')
    p.canvas.height = 500
    a.canvas.height = 500
    plan    = new Chart(p, dataset)
    actual  = new Chart(a, dataset)
    edit()
  }
</script>