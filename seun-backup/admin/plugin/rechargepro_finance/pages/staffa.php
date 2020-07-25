<?php
include "../../../engine.autoloader.php";
//require "../../../plugin/parking_core/parking_core.php";


?>
   <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>

<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>STAFF</th>
<th>TOTAL AGENT/DISTRIBUTOR</th>
<th>TOTAL SALES</th>
<th>rechargepro PROFIT</th>
<th>DISTRIBUTOR PROFIT</th>
<th>AGENT PROFIT</th>
<th>REFEREER PROFIT</th>
</tr>
</thead>
<tbody>
<?php

function  myname($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query("SELECT name FROM admin WHERE adminid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
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
$end = date('Y-m-d 23:23:23', strtotime('+0 day', strtotime($ex[1]))); ; 
    }


$permission =	$engine->admin_permission("rechargepro_transactionlog","staff");



$color=1;





$row = $engine->db_query2("SELECT * FROM (
  SELECT rechargepro_account.officer, SUM(rechargepro_transaction_log.amount) amt, rechargepro_transaction_log.rechargepro_service, rechargepro_transaction_log.rechargepro_subservice, SUM(rechargepro_transaction_log.refererprofit) AS rp, SUM(rechargepro_transaction_log.agentprofit) AS ap, SUM(rechargepro_transaction_log.cordprofit) AS cp, SUM(rechargepro_transaction_log.rechargeproprofit) as bp FROM rechargepro_transaction_log JOIN rechargepro_account ON rechargepro_account.rechargeproid = rechargepro_transaction_log.rechargeproid WHERE transaction_date BETWEEN ? AND ? AND rechargepro_subservice NOT IN('PROFIT','Debit','Credit','TRANSFER','TOPUP','REWARD') AND rechargepro_account.officer > '0'   AND rechargepro_transaction_log.rechargepro_status = 'PAID'
  GROUP BY officer
) v  ORDER BY amt DESC",array($start,$end));

     $officer = "0";           
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){

    $rechargeproprofit = $row[$dbc]['bp'];
    $cordprofit = $row[$dbc]['cp'];
    $agentprofit = $row[$dbc]['ap'];
    $refererprofit = $row[$dbc]['rp'];
    $rechargepro_subservice = $row[$dbc]['rechargepro_subservice'];
    $amount = $row[$dbc]['amt'];
    $cnt = "-";
    $officer = $row[$dbc]['officer'];

?>
<tr>
<td style="cursor: pointer;" onclick="load_myagentb('<?php echo $officer;?>')"><?php echo myname($officer,$engine);?></td>
<td><?php echo $cnt;?></td>
<td><?php echo "&#8358;".$amount;?></td>
<td><?php echo "&#8358;".$rechargeproprofit;?></td>
<td><?php echo "&#8358;".$cordprofit;?> </td>
<td><?php echo "&#8358;".$agentprofit;?></td>
<td><?php echo "&#8358;".$refererprofit;?></td>
</tr>
<?php
	}
    
    
 	if(!isset($amount)){echo "<div style='overflow:hidden; padding:5%;'> 
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>";}?>


</tbody>
</table>

</div>


<script type="text/javascript">
$(document).ready(function () { load_myagent('<?php echo $officer;?>','<?php echo $page;?>','<?php echo $type;?>');});
</script>





