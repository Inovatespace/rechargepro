<?php 
include "../../../../engine.autoloader.php";
$profile_creator = $engine->get_session("quickpayid");
$myprofile_role = $engine->get_session("quickpayrole");


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
$b = "(cordinator_id = '$profile_creator' ||  quickpayid = '$profile_creator')";
	break;

	case "2":
$b = "(agent_id = '$profile_creator' ||  quickpayid = '$profile_creator')";
	break;

	case "3":
$b = "quickpayid = '$profile_creator'";
	break;

	default :
    $b = "quickpayid = '$profile_creator'";
}


$call = "$b";

if(empty($user) && empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND transaction_date BETWEEN '$start' AND '$end'";
}


if(empty($user) && !empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND quickpay_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'";
}


if(!empty($user) && empty($service) && !empty($start) && !empty($end)){
    //depend
      $call = "quickpayid = '$profile_creator' AND transaction_date BETWEEN '$start' AND '$end'";  
}


if(!empty($user) && !empty($service) && empty($start) && empty($end)){
    //depend
    $call = "quickpayid = '$profile_creator' AND quickpay_subservice = '$service'";
}

if(!empty($user) && empty($service) && empty($start) && empty($end)){
    //depend
    $call = "quickpayid = '$profile_creator'";
}


if(empty($user) && !empty($service) && empty($start) && empty($end)){
    $call = "$b AND quickpay_subservice = '$service'";
}

if(!empty($user) && !empty($service) && !empty($start) && !empty($end)){
    //depend
   $call = "quickpayid = '$profile_creator' AND quickpay_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'"; 
}



?>
<script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>




<div style="overflow: hidden;">



<?php
$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

function merchant($id,$engine){
 $row = $engine->db_query("SELECT name FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($id));
 return $row[0]['name'];
}

$arrayuser = array();
$color=1;
if (isset($_REQUEST['q'])){
    
$q = $_REQUEST['q'];
switch ($myprofile_role){
	case "1":
$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,quickpayid,transaction_reference,amount,phone,transaction_status,quickpay_service,quickpay_subservice,account_meter,ip,quickpay_print,transaction_date, quickpay_print,quickpay_status_code,payment_method,quickpay_status_code,quickpay_status FROM quickpay_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND  (cordinator_id = ? ||  quickpayid = ?) AND quickpay_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%",$q,$profile_creator,$profile_creator));
	break;

	case "2":
$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,quickpayid,transaction_reference,amount,phone,transaction_status,quickpay_service,quickpay_subservice,account_meter,ip,quickpay_print,transaction_date, quickpay_print,payment_method,quickpay_status_code,quickpay_status FROM quickpay_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND (agent_id = ? || quickpayid = ?) AND quickpay_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%",$q,$profile_creator,$profile_creator));
	break;

	case "3":
$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,quickpayid,transaction_reference,amount,phone,transaction_status,quickpay_service,quickpay_subservice,account_meter,ip,quickpay_print,transaction_date, quickpay_print,payment_method,quickpay_status_code,quickpay_status FROM quickpay_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND quickpayid = ? AND quickpay_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%","%$q%",$profile_creator));
	break;

	default :
$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,quickpayid,transaction_reference,amount,phone,transaction_status,quickpay_service,quickpay_subservice,account_meter,ip,quickpay_print,transaction_date, quickpay_print,payment_method,quickpay_status_code,quickpay_status FROM quickpay_transaction_log WHERE (transaction_reference LIKE ? OR transactionid = ?) AND quickpayid = ? AND quickpay_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array("%$q%","%$q%",$profile_creator));
}

	}else{
	   

$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,quickpayid,transaction_reference,amount,phone,transaction_status,quickpay_service,quickpay_subservice,account_meter,ip,quickpay_print,transaction_date, quickpay_print,payment_method,quickpay_status_code,quickpay_status FROM quickpay_transaction_log WHERE $call  AND quickpay_status = 'PAID' ORDER BY transaction_date DESC LIMIT $start, $per_page",array($profile_creator));



}

for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['transactionid']; 
    $agent_id = $row[$dbc]['agent_id']; 
    $quickpayid = $row[$dbc]['quickpayid']; 
    $transaction_reference = $row[$dbc]['transaction_reference']; 
    $amount = $row[$dbc]['amount']; 
    
    
    $mob1 = substr($row[$dbc]['phone'], 0, 4);
    $mob2 = substr($row[$dbc]['phone'], -3, 11);
    
    $agentprofit = $row[$dbc]['agentprofit'];
    
    $phone =  $mob1."####".$mob2; 
    $transaction_status = $row[$dbc]['transaction_status']; 
    $quickpay_service = $row[$dbc]['quickpay_service']; 
    $quickpay_subservice = $row[$dbc]['quickpay_subservice']; 
    $account_meter = $row[$dbc]['account_meter']; 
    $ip = $row[$dbc]['ip']; 
    $quickpay_print = $row[$dbc]['quickpay_print']; 
    $transaction_date = $row[$dbc]['transaction_date'];
    $quickpay_status_code = $row[$dbc]['quickpay_status_code'];
$quickpay_status = $row[$dbc]['quickpay_status'];
$payment_method = $row[$dbc]['payment_method'];


    if(!array_key_exists($quickpayid,$arrayuser)){
    $arrayuser[$quickpayid] = merchant($quickpayid,$engine);
    }
    
    


$print = '<a href="/invoice&id='.$quickpayid."_".$id.'"><span class="fas fa-print" style="color:#21A537; cursor:pointer;">VIEW DETAILS</span></a>';
$sync = '';
if($quickpay_status_code == 0){
    $print = "&nbsp;";
    $sync = '<span class="fas fa-redo-alt" onclick="try_again(\''.$id.'\')" style="cursor:pointer; color:#AF2626;"></span>';}

if($quickpay_status_code == 0 && in_array($quickpay_service,array("SMS","BULK_AIRTIME"))){$snc = '';}




$bg = "";


//if($quickpay_subservice == "PROFIT"){$bg = "background-color:#DCF9FE;";}
//if($quickpay_subservice == "Debit" || $quickpay_subservice == "TRANSFER"){$bg = "background-color:#F7F0C3;";}
//if($quickpay_subservice == "Credit" || $quickpay_subservice == "TOPUP"){$bg = "background-color:#DCF9FE;";}



switch ($quickpay_subservice){ 
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
    if($quickpayid == $profile_creator){
    $sst = "Withdrawal from ".$arrayuser[$account_meter];
    }
	break;

	default : $sst = $quickpay_service." for ".$account_meter;
}


if(empty($transaction_status)){$transaction_status = "Successful";}
if($quickpay_status_code == "0"){$transaction_status = "Failed";}


$cl = "color:green;";
if($quickpay_status_code == "0"){$cl = "color:red;";}
?>    


<div style="border-bottom:solid 1px #EEEEEE; padding:5px; <?php echo $bg;?>">
<div style="overflow: hidden; margin-bottom:5px;">
<div style="float: left; font-size: 120%; font-weight:bold;"><?php echo $sst;?></div>
<div style="float: right;"><?php echo $engine->toMoney($amount);?> | <?php echo $engine->toMoney($agentprofit);?></div>
</div>


<div style="overflow: hidden; <?php echo $cl;?>">
<div style="float: left;"><strong style="color: black;"><?php echo $arrayuser[$quickpayid];?></strong> || <?php echo $sync;?> <?php echo substr($transaction_status, 0, 17);?> | <?php echo $transaction_date;?></div>
<div style="float: right;"><?php echo $print;?></div>
</div>
</div>

<?php
	}
?>
</div>