<?php 
include "../../../../../engine.autoloader.php";
$profile_creator = $engine->get_session("recharge4id");
$myprofile_role = $engine->get_session("recharge4role");


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
$b = "(cordinator_id = '$profile_creator' ||  recharge4id = '$profile_creator')";
	break;

	case "2":
$b = "(agent_id = '$profile_creator' ||  recharge4id = '$profile_creator')";
	break;

	case "3":
$b = "recharge4id = '$profile_creator'";
	break;

	default :
    $b = "recharge4id = '$profile_creator'";
}


$call = "$b";

if(empty($user) && empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND transaction_date BETWEEN '$start' AND '$end'";
}


if(empty($user) && !empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND recharge4_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'";
}


if(!empty($user) && empty($service) && !empty($start) && !empty($end)){
    //depend
      $call = "recharge4id = '$profile_creator' AND transaction_date BETWEEN '$start' AND '$end'";  
}


if(!empty($user) && !empty($service) && empty($start) && empty($end)){
    //depend
    $call = "recharge4id = '$profile_creator' AND recharge4_subservice = '$service'";
}

if(!empty($user) && empty($service) && empty($start) && empty($end)){
    //depend
    $call = "recharge4id = '$profile_creator'";
}


if(empty($user) && !empty($service) && empty($start) && empty($end)){
    $call = "$b AND recharge4_subservice = '$service'";
}

if(!empty($user) && !empty($service) && !empty($start) && !empty($end)){
    //depend
   $call = "recharge4id = '$profile_creator' AND recharge4_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'"; 
}



?>
<script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>




<div style="overflow: hidden;">

<table id="myTable" class="tablesorter" style="font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;; font-size:85%;">
<thead>
<tr style="text-transform: uppercase;">
<th>TID</th>
<th>ACCOUNT</th>
<th>SERVICE</th>
<th>AMOUNT</th>
<th>CURRENCY</th>
<th>STATUS</th>
<th>DATE</th>
<th>#</th>
</tr>
</thead>
<tbody>

<?php
$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

function merchant($id,$engine){
 $row = $engine->db_query("SELECT name FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($id));
 return $row[0]['name'];
}

$arrayuser = array();
$color=1;
if (isset($_REQUEST['q'])){
    
$q = $_REQUEST['q'];
switch ($myprofile_role){
	case "1":
$row = $engine->db_query("SELECT currency,agentprofit,transactionid,agent_id,recharge4id,transaction_reference,amount,phone,transaction_status,recharge4_service,recharge4_subservice,account_meter,ip,recharge4_print,transaction_date, recharge4_print,recharge4_status_code,payment_method,recharge4_status_code,recharge4_status FROM recharge4_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND  (cordinator_id = ? ||  recharge4id = ?) AND recharge4_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%",$q,$profile_creator,$profile_creator));
	break;

	case "2":
$row = $engine->db_query("SELECT currency,agentprofit,transactionid,agent_id,recharge4id,transaction_reference,amount,phone,transaction_status,recharge4_service,recharge4_subservice,account_meter,ip,recharge4_print,transaction_date, recharge4_print,payment_method,recharge4_status_code,recharge4_status FROM recharge4_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND (agent_id = ? || recharge4id = ?) AND recharge4_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%",$q,$profile_creator,$profile_creator));
	break;

	case "3":
$row = $engine->db_query("SELECT currency,agentprofit,transactionid,agent_id,recharge4id,transaction_reference,amount,phone,transaction_status,recharge4_service,recharge4_subservice,account_meter,ip,recharge4_print,transaction_date, recharge4_print,payment_method,recharge4_status_code,recharge4_status FROM recharge4_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND recharge4id = ? AND recharge4_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%","%$q%",$profile_creator));
	break;

	default :
$row = $engine->db_query("SELECT currency,agentprofit,transactionid,agent_id,recharge4id,transaction_reference,amount,phone,transaction_status,recharge4_service,recharge4_subservice,account_meter,ip,recharge4_print,transaction_date, recharge4_print,payment_method,recharge4_status_code,recharge4_status FROM recharge4_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND recharge4id = ? AND recharge4_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%","%$q%",$profile_creator));
}

	}else{
	   

$row = $engine->db_query("SELECT currency,agentprofit,transactionid,agent_id,recharge4id,transaction_reference,amount,phone,transaction_status,recharge4_service,recharge4_subservice,account_meter,ip,recharge4_print,transaction_date, recharge4_print,payment_method,recharge4_status_code,recharge4_status FROM recharge4_transaction_log WHERE $call  AND recharge4_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array($profile_creator));



}

for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['transactionid']; 
    $agent_id = $row[$dbc]['agent_id']; 
    $recharge4id = $row[$dbc]['recharge4id']; 
    $transaction_reference = $row[$dbc]['transaction_reference']; 
    $amount = $row[$dbc]['amount']; 
    $currency = $row[$dbc]['currency']; 
    
    $mob1 = substr($row[$dbc]['phone'], 0, 4);
    $mob2 = substr($row[$dbc]['phone'], -3, 11);
    
    $agentprofit = $row[$dbc]['agentprofit'];
    
    $phone =  $mob1."####".$mob2; 
    $transaction_status = $row[$dbc]['transaction_status']; 
    $recharge4_service = $row[$dbc]['recharge4_service']; 
    $recharge4_subservice = $row[$dbc]['recharge4_subservice']; 
    $account_meter = $row[$dbc]['account_meter']; 
    $ip = $row[$dbc]['ip']; 
    $recharge4_print = $row[$dbc]['recharge4_print']; 
    $transaction_date = $row[$dbc]['transaction_date'];
    $recharge4_status_code = $row[$dbc]['recharge4_status_code'];
$recharge4_status = $row[$dbc]['recharge4_status'];
$payment_method = $row[$dbc]['payment_method'];


    if(!array_key_exists($recharge4id,$arrayuser)){
    $arrayuser[$recharge4id] = merchant($recharge4id,$engine);
    }
    
    


$print = '<a href="/invoice&id='.$recharge4id."_".$id.'"><span class="fas fa-print" style="color:#21A537; cursor:pointer;"></span></a>';
$sync = '';
if($recharge4_status_code == 0){
    $print = '<span class="fas fa-redo-alt" onclick="try_again(\''.$id.'\')" style="cursor:pointer; color:#AF2626;"></span>';}

if($recharge4_status_code == 0 && in_array($recharge4_service,array("SMS","BULK_AIRTIME"))){$snc = '';}




$bg = "";


if($recharge4_subservice == "PROFIT"){$bg = "background-color:#DCF9FE;";}
if($recharge4_subservice == "Debit" || $recharge4_subservice == "TRANSFER"){$bg = "background-color:#F7F0C3;";}
if($recharge4_subservice == "Credit" || $recharge4_subservice == "TOPUP"){$bg = "background-color:#DCF9FE;";}



switch ($recharge4_subservice){ 
	case "REWARD": 
    if(!array_key_exists($account_meter,$arrayuser)){
    $arrayuser[$account_meter] = merchant($account_meter,$engine);
    }
    $sst = "Reward from ".$arrayuser[$account_meter];
	break;

	case "TRANSFER":     
    if(!array_key_exists($account_meter,$arrayuser)){
    $arrayuser[$account_meter] = merchant($account_meter,$engine);
    }
    $sst = "Transfer to ".$arrayuser[$account_meter];
	break;

	case "TOPUP":     
    if(!array_key_exists($account_meter,$arrayuser)){
    $arrayuser[$account_meter] = merchant($account_meter,$engine);
    }
    $sst = "Top up From ".$arrayuser[$account_meter];
	break;
    
	case "WITHDRAW":   
    if(!array_key_exists($account_meter,$arrayuser)){
    $arrayuser[$account_meter] = merchant($account_meter,$engine);
    }
    $sst = "Withdrawal by ".$arrayuser[$account_meter];
    if($recharge4id == $profile_creator){
    $sst = "Withdrawal from ".$arrayuser[$account_meter];
    }
	break;

	default : $sst = $recharge4_service." for ".$account_meter;
}


if(empty($transaction_status)){$transaction_status = "Successful";}
if($recharge4_status_code == "0"){$transaction_status = "Failed";}


$cl = "color:green;";
if($recharge4_status_code == "0"){$cl = "color:red;";}
?>    
<tr>
<td><?php echo $recharge4id."_".$id;?></td>
<td><?php echo $arrayuser[$recharge4id];?></td>
<td><?php echo $sst;?></td>
<td><?php echo $engine->toMoney(($amount-$agentprofit));?></td>
<td><?php echo $currency;?></td>
<td><?php echo $transaction_status;?></td>
<td><?php echo $transaction_date;?></td>
<td><?php echo $print;?></td>
</tr>

<?php
	}
?>

</tbody>
</table>
</div>