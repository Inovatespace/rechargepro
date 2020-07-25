<?php
include "../../../engine.autoloader.php";
function rangeMonth($datestr){
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d 23:59:59', strtotime('last day of this month', $dt));
    return $res;
}

function rangeWeek($datestr){
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('monday this week'));
    $res['end'] = date('Y-m-d 23:59:59', strtotime('sunday this week'));
    return $res;
}
 
$today = date("Y-m-d");


$page = $_REQUEST['page'];
$type = $_REQUEST['type'];


if($type == 1){
    
if($page == "1"){
$start = $today;
$end = date('Y-m-d 23:59:59', strtotime('+0 day', strtotime($start))); 
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
$end = date('Y-m-d 23:59:59', strtotime('+0 day', strtotime($ex[1])));; 
    }

?>



<div class="profilebg" id="acholder" style="padding:10px; border:solid 1px #EEEEEE; overflow:hidden;">




<?php

  


?>
<div>


<div id="container" style="width: 100%; height: 300px;"></div>




</div>

<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />


<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>PRODUCT</th>
<th>TOTAL VOLUME</th>
<th>TOTAL SALES</th>
<th>rechargepro PROFIT</th>
<th>DISTRIBUTOR PROFIT</th>
<th>AGENT PROFIT</th>
<th>REFEREER PROFIT</th>
</tr>
</thead>
<tbody>

<?php
$total_tid = 0;
$total_amount = 0;
$total_rechargeproprofit = 0;
$total_cordprofit = 0;
$total_agentprofit = 0;
$total_refererprofit = 0;


$mytid = array();
$row = $engine->db_query2("SELECT tid FROM refund_process",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $mytid[] = $row[$dbc]['tid'];
    
    }

$mytid = implode(",",$mytid);

$array_special = array();
$array = array();
$chartarray = array();
$row = $engine->db_query2("SELECT SUM(amount) amt, COUNT(transactionid) AS tid, rechargepro_service, rechargepro_subservice, SUM(refererprofit) AS rp, SUM(agentprofit) AS ap, SUM(cordprofit) AS cp, SUM(rechargeproprofit) as bp FROM rechargepro_transaction_log WHERE ((rechargepro_status = 'PAID' OR rechargepro_service = 'CREDIT' OR rechargepro_service = 'Debit') AND rechargepro_service != 'TRANSFER' AND rechargepro_service != 'PROFIT' AND rechargepro_service != 'REWARD' AND rechargepro_service != 'TOPUP' AND rechargepro_service != 'WITHDRAW' AND transactionid NOT IN ($mytid) )  AND transaction_date BETWEEN ? AND ? GROUP BY  rechargepro_subservice",array($start,$end));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $tid = $row[$dbc]['tid'];
    $rechargeproprofit = $row[$dbc]['bp'];
    $cordprofit = $row[$dbc]['cp'];
    $agentprofit = $row[$dbc]['ap'];
    $refererprofit = $row[$dbc]['rp'];
    $rechargepro_subservice = $row[$dbc]['rechargepro_subservice'];
    $amount = $row[$dbc]['amt'];
    $rechargepro_service = $row[$dbc]['rechargepro_service'];
    
   
    if (strpos($rechargepro_service, 'REFUND') !== false) {
    $rechargepro_service = "REFUND";
    }
    
    
    if(!in_array($rechargepro_service,array("Credit","Debit","REFUND","Loan Payment","loan Credit","AUTO TOPUP","AUTO PAY"))){
    $array[] = $rechargepro_service;
   
    $chartarray[$rechargepro_service] = array($amount,$rechargeproprofit,$cordprofit,$agentprofit,$refererprofit);    
        
    $total_tid = $total_tid + $tid;
    $total_amount = $total_amount + $amount;
    $total_rechargeproprofit = $total_rechargeproprofit + $rechargeproprofit;
    $total_cordprofit = $total_cordprofit + $cordprofit;
    $total_agentprofit = $total_agentprofit + $agentprofit;
    $total_refererprofit = $total_refererprofit +$refererprofit;
    }
    
        if(in_array($rechargepro_service,array("Credit","Debit","REFUND","Loan Payment","loan Credit","AUTO TOPUP","AUTO PAY"))){
        $refererprofit = 0;
        $agentprofit = 0;
        $cordprofit = 0;
        $rechargeproprofit = 0;
        }

?>
<tr >
<td><?php echo $rechargepro_service;?></td>
<td><?php echo $tid;?></td>
<td><?php echo $engine->toMoney($amount,"&#8358;");?></td>
<td><?php echo $engine->toMoney($rechargeproprofit,"&#8358;");?></td>
<td><?php echo $engine->toMoney($cordprofit,"&#8358;");?> </td>
<td><?php echo $engine->toMoney($agentprofit,"&#8358;");?></td>
<td><?php echo $engine->toMoney($refererprofit,"&#8358;");?></td>
</tr>
<?php
	}
    ?>
<tr style="font-weight: bold;" >
<td>TOTAL</td>
<td><?php echo $total_tid;?></td>
<td><?php echo $engine->toMoney($total_amount,"&#8358;");?></td>
<td><?php echo $engine->toMoney($total_rechargeproprofit,"&#8358;");?></td>
<td><?php echo $engine->toMoney($total_cordprofit,"&#8358;");?> </td>
<td><?php echo $engine->toMoney($total_agentprofit,"&#8358;");?></td>
<td><?php echo $engine->toMoney($total_refererprofit,"&#8358;");?></td>
</tr>
    <?php
    
$newarray = array();
for ($i = 0; $i < count($array); $i++){
    
    if(isset($chartarray[$array[$i]])){
    for($ii = 0; $ii <= count($chartarray[$array[$i]]); $ii++){
    $newarray["totalsales"][$i] = $chartarray[$array[$i]][0];
    $newarray["rechargeprosales"][$i] = $chartarray[$array[$i]][1];
    $newarray["cordinatorsales"][$i] = $chartarray[$array[$i]][2];
    $newarray["agentsales"][$i] = $chartarray[$array[$i]][3];
    $newarray["refereersales"][$i] = $chartarray[$array[$i]][4];
    }
    }
}


?>
</tbody>
</table>



<script type="text/javascript"> 
    $(function () {
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },credits: {
enabled: false
},
    title: {
        text: 'Sales chart by Product'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        categories: [<?php foreach($chartarray AS $key => $val){ echo "'$key',";}?>
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Amount'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [<?php foreach($newarray AS $key => $val){ $count = ""; foreach($val AS $o){$count .= $o.",";}  echo "{name: '$key',data: [$count]},";}?>]
});
    
    
    
});
</script>





<div  style="border-top: solid 2px #4E1010; margin-top:20px; margin-bottom:20px;"></div>