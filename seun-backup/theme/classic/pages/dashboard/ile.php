<?php 
$engine = new engine();
?>
<div style="background-color: white;">
<div style="padding:10px; margin-bottom:10px; min-height: 500px;">


<img src="/theme/classic/images/in.jpg" width="99%" style="padding: 10px; background-color: white; border: solid 1px #EEEEEE;" />

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<?php
$today = date("Y-m-d"); 
function rangeMonth($datestr){
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d 23:23:59', strtotime('last day of this month', $dt));
    return $res;
}
 $recharge4id = $engine->get_session("recharge4id");
$range = rangeMonth($today);
$start = $range['start'];
$end = $range['end'];
  
$chartarray = array();
$row = $engine->db_query("SELECT COUNT(transactionid) AS ccount, DATE_FORMAT(transaction_date, '%d') AS day FROM recharge4_transaction_log WHERE recharge4id = ? AND transaction_date BETWEEN ? AND ? GROUP BY day ORDER BY day ASC",array($recharge4id,$start,$end));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    
         $chartarray[$row[$dbc]['day']] = $row[$dbc]['ccount'];   
        
    
    }	
?>
<script type="text/javascript">
    $(function () {
        
        
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },credits: {
enabled: false
},
    title: {
        text: 'Daily transaction for current month'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Transaction'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: 'Transaction {point.y:.1f}'
    },
    series: [{
        name: 'Transaction',
        colorByPoint: true,
        data: [<?php  foreach($chartarray AS $key => $val){echo "['$key', $val],";} ?>],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y:.1f}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});
        })
</script>



</div></div>

