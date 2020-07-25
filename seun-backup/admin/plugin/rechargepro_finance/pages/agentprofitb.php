<?php
include "../../../engine.autoloader.php";

function  myname($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id)); 
    return substr($row[0]['name'], 0, 10);
}


function rangeMonth($datestr){
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d 23:23:59', strtotime('last day of this month', $dt));
    return $res;
}

function rangeWeek($datestr){
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('monday this week'));
    $res['end'] = date('Y-m-d 23:23:59', strtotime('sunday this week'));
    return $res;
}
 
$today = date("Y-m-d");


$page = $_REQUEST['page'];
$type = $_REQUEST['type'];


if($type == 1){
    
if($page == "1"){
$start = $today;
$end = date('Y-m-d 23:23:23', strtotime('+0 day', strtotime($start))); 
}

if($page == "2"){
$range = rangeWeek($today);
$start = $range['start'];
$end = $range['end']; 
}

if($page == "3"){
$range = rangeMonth($today);
$start = $range['start'];
$end = $range['end']; 
}

}


if($type == 2){
$ex = explode("@",$page);
$start = $ex[0];
$end = $ex[1]; 
    }





  
$chartarray = array();
//$row = $engine->db_query2("SELECT SUM(amount) as amt, cordinator_id, DATE_FORMAT(transaction_date, '%d') AS day FROM rechargepro_refund WHERE transaction_date BETWEEN ? AND ? GROUP BY cordinator_id ORDER BY amt DESC",array($start,$end));
$row = $engine->db_query2("SELECT v.amt, v.cordinator_id FROM (
  SELECT SUM(amount) as amt, cordinator_id
  FROM rechargepro_transaction_log WHERE transaction_date BETWEEN ? AND ? AND cordinator_id != '0'
  AND rechargepro_status = ? GROUP BY cordinator_id
) v  ORDER BY amt DESC LIMIT 20",array($start,$end,"PAID"));

    for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $chartarray[myname($row[$dbc]['cordinator_id'],$engine)] = $row[$dbc]['amt'];
    }
?>

<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">DISTRIBUTORS TRANSACTION</div></div>

<div id="container" style="width: 100%; height: 250px;"></div>
<script type="text/javascript">
    $(function () {
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },credits: {
enabled: false
},
    title: {
        text: 'TOP 20 DISTRIBUTORS'
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
            text: 'AMOUNT'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: '{point.y}</b>'
    },
    series: [{
        name: 'AMOUNT',
        data: [ <?php foreach($chartarray AS $key => $val){echo "['".$key."', ".$val."],";}?>],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});

});
</script>




<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">AGENT TRANSACTION</div></div>


<?php
$chartarray = array();
$row = $engine->db_query2("SELECT v.amt, v.agent_id FROM (
  SELECT SUM(amount) as amt, agent_id
  FROM rechargepro_transaction_log WHERE transaction_date BETWEEN ? AND ? AND agent_id != '0'
    AND rechargepro_status = ? GROUP BY agent_id
) v  ORDER BY amt DESC LIMIT 20",array($start,$end,"PAID"));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
  
    $chartarray[myname($row[$dbc]['agent_id'],$engine)] = $row[$dbc]['amt'];
 
    }
?>

<div id="container2" style="width: 100%; height: 250px;"></div>
<script type="text/javascript">
    $(function () {
Highcharts.chart('container2', {
    chart: {
        type: 'column'
    },credits: {
enabled: false
},
    title: {
        text: 'TOP 20 AGENT'
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
            text: 'AMOUNT'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: '{point.y}</b>'
    },
    series: [{
        name: 'AMOUNT',
        data: [ <?php foreach($chartarray AS $key => $val){echo "['".$key."', ".$val."],";}?>],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});

});
</script>




<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">STAFF TRANSACTION</div></div>


<?php
$chartarray = array();
$row = $engine->db_query2("SELECT * FROM (
  SELECT SUM(amount) as amt, rechargeproid
  FROM rechargepro_transaction_log WHERE transaction_date BETWEEN ? AND ? AND rechargeproid IN (1,34)  AND rechargepro_status = ?
  GROUP BY rechargeproid
) v ORDER BY amt DESC LIMIT 20",array($start,$end,"PAID"));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
  
    $chartarray[myname($row[$dbc]['rechargeproid'],$engine)] = $row[$dbc]['amt'];
 
    }
?>

<div id="container3" style="width: 100%; height: 250px;"></div>
<script type="text/javascript">
    $(function () {
Highcharts.chart('container3', {
    chart: {
        type: 'column'
    },credits: {
enabled: false
},
    title: {
        text: 'TOP 20 STAFF'
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
            text: 'AMOUNT'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: '{point.y}</b>'
    },
    series: [{
        name: 'AMOUNT',
        data: [ <?php foreach($chartarray AS $key => $val){echo "['".$key."', ".$val."],";}?>],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});

});
</script>




