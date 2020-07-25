<?php 
include "../../../../engine.autoloader.php";
$profile_creator = $engine->get_session("rechargeproid");
$myprofile_role = $engine->get_session("rechargeprorole");


$start = "";
$end = "";
$service = "";
$user = "";
$server = $engine->safe_html($_REQUEST['server']);
if(!empty($server)){
    $ex = explode("@@",$server);
    
    $start = $ex[0];
    $end = date('Y-m-d 23:23:59', strtotime('+0 days', strtotime($ex[1])));
    if(empty($ex[1])){$end = "";}
    $service = $ex[3];
    $user = $ex[2];
    
    if($user == "All"){$user = "";}
    if($service == "All"){$service = "";}
}

if(!empty($user)){$profile_creator = $user;}
switch ($myprofile_role){
	case "1":
$b = "(cordinator_id = '$profile_creator' ||  rechargeproid = '$profile_creator')";
	break;

	case "2":
$b = "(agent_id = '$profile_creator' ||  rechargeproid = '$profile_creator')";
	break;

	case "3":
$b = "rechargeproid = '$profile_creator'";
	break;

	default :
    $b = "rechargeproid = '$profile_creator'";
}


$call = "$b";

if(empty($user) && empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND transaction_date BETWEEN '$start' AND '$end'";
}


if(empty($user) && !empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND rechargepro_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'";
}


if(!empty($user) && empty($service) && !empty($start) && !empty($end)){
    //depend
      $call = "$b AND transaction_date BETWEEN '$start' AND '$end'";  
}


if(!empty($user) && !empty($service) && empty($start) && empty($end)){
    //depend
    $call = "$b AND rechargepro_subservice = '$service'";
}

if(!empty($user) && empty($service) && empty($start) && empty($end)){
    //depend
    $call = "$b";
}


if(empty($user) && !empty($service) && empty($start) && empty($end)){
    $call = "$b AND rechargepro_subservice = '$service'";
}

if(!empty($user) && !empty($service) && !empty($start) && !empty($end)){
    //depend
   $call = "$b AND rechargepro_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'"; 
}



?>
<script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>
<script type="text/javascript">
$(document).ready(function(){$("#myTable").tablesorter();});
</script>






















<style type="text/css">
.limiter {
  width: 100%;
  margin: 0 auto;
}

.container-table100 {
  width: 100%;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
 
}


/*//////////////////////////////////////////////////////////////////
[ Table ]*/
table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

th, td {
  font-weight: unset;
}

.column100 {
  max-width: 30%;
}

.column100.column1 {
  max-width: 30%;
}

.row100.head th {
  padding: 3px 5px;
}

.row100 td {
  padding: 3px 5px;
}


/*==================================================================
[ Ver3 ]*/
tr{
  border-bottom: 1px solid #EEEEEE;
      display: table-row;
    border-color: inherit;
}

.table100.ver3 td {
  color: #808080;
}

.table100.ver3 th {
  color: #fff;
  text-transform: uppercase;

  background-color: #6c7ae0;
}

.table100.ver3 .row100:hover td {
  background-color: #fcebf5;
}

.table100.ver3 .hov-column-ver3 {
  background-color: #fcebf5;
}


.table100.ver3 .row100 td:hover {
  background-color: #e03e9c;
  color: #fff;
}

</style>
<script type="text/javascript">
(function ($) {
	"use strict";
	$('.column100').on('mouseover',function(){
		var table1 = $(this).parent().parent().parent();
		var table2 = $(this).parent().parent();
		var verTable = $(table1).data('vertable')+"";
		var column = $(this).data('column') + ""; 

		$(table2).find("."+column).addClass('hov-column-'+ verTable);
		$(table1).find(".row100.head ."+column).addClass('hov-column-head-'+ verTable);
	});

	$('.column100').on('mouseout',function(){
		var table1 = $(this).parent().parent().parent();
		var table2 = $(this).parent().parent();
		var verTable = $(table1).data('vertable')+"";
		var column = $(this).data('column') + ""; 

		$(table2).find("."+column).removeClass('hov-column-'+ verTable);
		$(table1).find(".row100.head ."+column).removeClass('hov-column-head-'+ verTable);
	});
    

})(jQuery);
</script>
		
<div class="limiter">
<div class="container-table100">
<div class="table100 ver3 m-b-110">
					<table data-vertable="ver3">
						<thead>
							<tr class="row100 head">
<th class="column100 column1" data-column="column1">AGENT ID</th>
<th class="column100 column2" data-column="column2">AMOUNT</th>
<th class="column100 column3" data-column="column3">PROFIT</th>
<th class="column100 column4" data-column="column5">STATUS</th>
<th class="column100 column4" data-column="column6">SERVICE</th>
<th class="column100 column4" data-column="column7">AC/PHONE</th>
<th class="column100 column4" data-column="column8">DATE</th>
<th class="column100 column4" data-column="column9">REFUND</th>
<th class="column100 column4" data-column="column10">PRINT</th>
							</tr>
						</thead>
						<tbody>
<?php
$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

function merchant($id,$engine){
 $row = $engine->db_query("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));
 return $row[0]['name'];
}

$arrayuser = array();
$color=1;
if (isset($_REQUEST['q'])){
    
$q = $_REQUEST['q'];
switch ($myprofile_role){
	case "1":
$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,rechargeproid,transaction_reference,amount,phone,transaction_status,rechargepro_service,rechargepro_subservice,account_meter,ip,rechargepro_print,transaction_date, rechargepro_print,rechargepro_status_code,payment_method,rechargepro_status_code,rechargepro_status FROM rechargepro_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND  (cordinator_id = ? ||  rechargeproid = ?) AND rechargepro_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%",$q,$profile_creator,$profile_creator));
	break;

	case "2":
$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,rechargeproid,transaction_reference,amount,phone,transaction_status,rechargepro_service,rechargepro_subservice,account_meter,ip,rechargepro_print,transaction_date, rechargepro_print,payment_method,rechargepro_status_code,rechargepro_status FROM rechargepro_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND (agent_id = ? || rechargeproid = ?) AND rechargepro_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%",$q,$profile_creator,$profile_creator));
	break;

	case "3":
$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,rechargeproid,transaction_reference,amount,phone,transaction_status,rechargepro_service,rechargepro_subservice,account_meter,ip,rechargepro_print,transaction_date, rechargepro_print,payment_method,rechargepro_status_code,rechargepro_status FROM rechargepro_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND rechargeproid = ? AND rechargepro_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%","%$q%",$profile_creator));
	break;

	default :
$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,rechargeproid,transaction_reference,amount,phone,transaction_status,rechargepro_service,rechargepro_subservice,account_meter,ip,rechargepro_print,transaction_date, rechargepro_print,payment_method,rechargepro_status_code,rechargepro_status FROM rechargepro_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND rechargeproid = ? AND rechargepro_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%","%$q%",$profile_creator));
}

	}else{
	   

$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,rechargeproid,transaction_reference,amount,phone,transaction_status,rechargepro_service,rechargepro_subservice,account_meter,ip,rechargepro_print,transaction_date, rechargepro_print,payment_method,rechargepro_status_code,rechargepro_status FROM rechargepro_transaction_log WHERE $call  AND rechargepro_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array($profile_creator));



}

for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['transactionid']; 
    $agent_id = $row[$dbc]['agent_id']; 
    $rechargeproid = $row[$dbc]['rechargeproid']; 
    $transaction_reference = $row[$dbc]['transaction_reference']; 
    $amount = $row[$dbc]['amount']; 
    
    
    $mob1 = substr($row[$dbc]['phone'], 0, 4);
    $mob2 = substr($row[$dbc]['phone'], -3, 11);
    
    $agentprofit = $row[$dbc]['agentprofit'];
    
    $phone =  $mob1."####".$mob2; 
    $transaction_status = $row[$dbc]['transaction_status']; 
    $rechargepro_service = $row[$dbc]['rechargepro_service']; 
    $rechargepro_subservice = $row[$dbc]['rechargepro_subservice']; 
    $account_meter = $row[$dbc]['account_meter']; 
    $ip = $row[$dbc]['ip']; 
    $rechargepro_print = $row[$dbc]['rechargepro_print']; 
    $transaction_date = $row[$dbc]['transaction_date'];
    $rechargepro_status_code = $row[$dbc]['rechargepro_status_code'];
$rechargepro_status = $row[$dbc]['rechargepro_status'];
$payment_method = $row[$dbc]['payment_method'];
    if(!array_key_exists($rechargeproid,$arrayuser)){
    $arrayuser[$rechargeproid] = merchant($rechargeproid,$engine);
    }

$print = '<a href="/invoice&id='.$rechargeproid."_".$id.'"><span class="fas fa-print" style="color:#21A537; cursor:pointer;"></span></a>';
$sync = '-';
if($rechargepro_status_code == 0){
    $print = "-";
    $sync = '<span class="fas fa-redo-alt" onclick="try_again(\''.$id.'\')" style="cursor:pointer; color:#AF2626;"></span>';}

if($rechargepro_status_code == 0 && in_array($rechargepro_service,array("SMS","BULK_AIRTIME"))){$snc = '';}




$bg = "";


if($rechargepro_subservice == "PROFIT"){$bg = "background-color:#DCF9FE;";}
if($rechargepro_subservice == "Debit" || $rechargepro_subservice == "TRANSFER"){$bg = "background-color:#F7F0C3;";}
if($rechargepro_subservice == "Credit" || $rechargepro_subservice == "TOPUP"){$bg = "background-color:#DCF9FE;";}



switch ($rechargepro_subservice){ 
	case "REWARD": $sst = "Reward from ";
	break;

	case "TRANSFER": $sst = "Transfer to ";
	break;

	case "TOPUP": $sst = "Top up From ";
	break;
    
    
	case "WITHDRAW": 
    $sst = "Withdrawal by";
    if($agentprofit == $profile_creator){
    $sst = "Withdrawal from";
    }
	break;

	default : $sst = $rechargepro_service." for ".$account_meter;
}
?>    



           
<tr  class="row100" style="<?php echo $bg;?>">
<td  class="column100 column1" data-column="column1"><?php echo $arrayuser[$rechargeproid];?></td>
<td  class="column100 column2" data-column="column2"><?php echo $engine->toMoney($amount);?></td>
<td  class="column100 column3" data-column="column3"><?php echo $agentprofit;?></td>
<td  class="column100 column4" data-column="column4"><?php echo $transaction_status;?></td>
<td  class="column100 column5" data-column="column5"><?php echo $rechargepro_service;?></td>
<td  class="column100 column6" data-column="column6"><?php echo $account_meter;?></td>
<td  class="column100 column7" data-column="column7"><?php echo $transaction_date;?></td>
<td  class="column100 column8" data-column="column8"><?php echo $sync;?></td>
<td  class="column100 column9" data-column="column9"><?php echo $print;?></td>
</tr>
<?php
	}
?>
</tbody>
</table>
</div></div></div>