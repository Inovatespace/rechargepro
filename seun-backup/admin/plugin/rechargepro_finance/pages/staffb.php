<?php
include "../../../engine.autoloader.php";
//require "../../../plugin/parking_core/parking_core.php";


?>
   <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>
<?php
$id = $_REQUEST['id'];

function  mynameb($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query("SELECT name FROM admin WHERE adminid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
}

function  myname($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id)); 
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


?>
<div style="margin-top: 20px;" class="nInformation"><?php echo mynameb($id,$engine);?>, Agent's activity for <?php echo $start;?> to <?php echo $end;?></div>

<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>SN</th>
<th>STAFF</th>
<th>NUMBER OF TRANSACTION</th>
<th>TOTAL SALES</th>
<th>rechargepro PROFIT</th>
<th>DISTRIBUTOR PROFIT</th>
<th>AGENT PROFIT</th>
<th>REFEREER PROFIT</th>
</tr>
</thead>
<tbody>
<?php

$permission =	$engine->admin_permission("rechargepro_transactionlog","staff");



$color=1;
$totalamount = 0;
$totalrechargeproprofit = 0;
$totalcordprofit = 0;
$totalagentprofit = 0;
$totalrefererprofit = 0;

$mytid = array();
$row = $engine->db_query2("SELECT tid FROM refund_process",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $mytid[] = $row[$dbc]['tid'];
    
    }

$mytid = implode(",",$mytid);


$row = $engine->db_query2("SELECT * FROM (
  SELECT rechargepro_transaction_log.rechargeproid, COUNT(rechargepro_transaction_log.transactionid) cnt, SUM(rechargepro_transaction_log.amount) amt, rechargepro_transaction_log.rechargepro_service, rechargepro_transaction_log.rechargepro_subservice, SUM(rechargepro_transaction_log.refererprofit) AS rp, SUM(rechargepro_transaction_log.agentprofit) AS ap, SUM(rechargepro_transaction_log.cordprofit) AS cp, SUM(rechargepro_transaction_log.rechargeproprofit) as bp FROM rechargepro_transaction_log JOIN rechargepro_account ON rechargepro_account.rechargeproid = rechargepro_transaction_log.rechargeproid WHERE transaction_date BETWEEN ? AND ? AND rechargepro_subservice NOT IN('PROFIT','Debit','Credit','TRANSFER','TOPUP','REWARD') AND rechargepro_account.officer = ?  AND rechargepro_transaction_log.rechargepro_status = 'PAID' AND rechargepro_transaction_log.transactionid NOT IN ($mytid)
  GROUP BY rechargeproid
) v  ORDER BY amt DESC",array($start,$end,$id));

                
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$sn++;
    $rechargeproprofit = $row[$dbc]['bp'];
    $cordprofit = $row[$dbc]['cp'];
    $agentprofit = $row[$dbc]['ap'];
    $refererprofit = $row[$dbc]['rp'];
    $rechargepro_subservice = $row[$dbc]['rechargepro_subservice'];
    $amount = $row[$dbc]['amt'];
    $cnt = $row[$dbc]['cnt'];
    $rechargeproid = $row[$dbc]['rechargeproid'];

?>
<tr >
<td><?php echo $sn;?></td>
<td><?php echo myname($rechargeproid,$engine);?></td>
<td><?php echo $cnt;?></td>
<td><?php echo "&#8358;".$amount;?></td>
<td><?php echo "&#8358;".$rechargeproprofit;?></td>
<td><?php echo "&#8358;".$cordprofit;?> </td>
<td><?php echo "&#8358;".$agentprofit;?></td>
<td><?php echo "&#8358;".$refererprofit;?></td>
</tr>
<?php 
$totalamount = $totalamount + $amount;
$totalrechargeproprofit = $totalrechargeproprofit + $rechargeproprofit;
$totalcordprofit = $totalcordprofit + $cordprofit;
$totalagentprofit = $totalagentprofit + $agentprofit;
$totalrefererprofit = $totalrefererprofit + $refererprofit;


	}
    if(isset($amount)){?>
       <tr style="font-weight: bold;"> 
<td colspan="7"></td>
</tr>
 
    <tr style="font-weight: bold;"> 
<td>TOTAL</td>
<td>-</td>
<td><?php echo "&#8358;".$totalamount;?></td>
<td><?php echo "&#8358;".$totalrechargeproprofit;?></td>
<td><?php echo "&#8358;".$totalcordprofit;?> </td>
<td><?php echo "&#8358;".$totalagentprofit;?></td>
<td><?php echo "&#8358;".$totalrefererprofit;?></td>
</tr>
    <?php
	}

    
    
 	if(!isset($amount)){  echo "<div style='overflow:hidden; padding:5%;'> 
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>";}?>


</tbody>
</table>

</div>





