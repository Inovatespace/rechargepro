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
      $call = "rechargeproid = '$profile_creator' AND transaction_date BETWEEN '$start' AND '$end'";  
}


if(!empty($user) && !empty($service) && empty($start) && empty($end)){
    //depend
    $call = "rechargeproid = '$profile_creator' AND rechargepro_subservice = '$service'";
}

if(!empty($user) && empty($service) && empty($start) && empty($end)){
    //depend
    $call = "rechargeproid = '$profile_creator'";
}


if(empty($user) && !empty($service) && empty($start) && empty($end)){
    $call = "$b AND rechargepro_subservice = '$service'";
}

if(!empty($user) && !empty($service) && !empty($start) && !empty($end)){
    //depend
   $call = "rechargeproid = '$profile_creator' AND rechargepro_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'"; 
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
 $row = $engine->db_query("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));
 return $row[0]['name'];
}

$arrayuser = array();
$color=1;
if (isset($_REQUEST['q'])){
    
$q = $_REQUEST['q'];

$row = $engine->db_query("SELECT agentprofit,transactionid,agent_id,rechargeproid,transaction_reference,amount,phone,transaction_status,rechargepro_service,rechargepro_subservice,account_meter,ip,rechargepro_print,transaction_date, rechargepro_print,payment_method,rechargepro_status_code,rechargepro_status FROM rechargepro_transaction_log WHERE $call  AND rechargepro_status = 'PAID' AND (account_meter = ? OR transactionid = ?) ORDER BY transaction_date DESC LIMIT 50",array($q,$q));

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
    
    


$print = '<a href="/invoice&id='.$rechargeproid."_".$id.'"><span class="fas fa-print" style="color:#21A537; cursor:pointer;"> MORE</span></a>';
$sync = '';
if($rechargepro_status_code == 0){
    $print = "-";
    $sync = '<span class="fas fa-redo-alt" onclick="try_again(\''.$id.'\')" style="cursor:pointer; color:#AF2626;"></span>';}

if($rechargepro_status_code == 0 && in_array($rechargepro_service,array("SMS","BULK_AIRTIME"))){$snc = '';}




$bg = "";


if($rechargepro_subservice == "PROFIT"){$bg = "background-color:#DCF9FE;";}
if($rechargepro_subservice == "Debit" || $rechargepro_subservice == "TRANSFER"){$bg = "background-color:#F7F0C3;";}
if($rechargepro_subservice == "Credit" || $rechargepro_subservice == "TOPUP"){$bg = "background-color:#DCF9FE;";}



switch ($rechargepro_subservice){ 
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
    if($rechargeproid == $profile_creator){
    $sst = "Withdrawal from ".$arrayuser[$account_meter];
    }
	break;

	default : $sst = $rechargepro_service." for ".$account_meter;
}


if(empty($transaction_status)){$transaction_status = "Successful";}
if($rechargepro_status_code == "0"){$transaction_status = "Failed";}


$cl = "color:green;";
if($rechargepro_status_code == "0"){$cl = "color:red;";}
?>    


<div style="border-bottom:solid 1px #EEEEEE; padding:5px; <?php echo $bg;?>">
<div style="overflow: hidden; margin-bottom:5px;">
<div style="float: left; font-size: 120%; font-weight:bold;"><?php echo $sst;?></div>
<div style="float: right;"><?php echo $engine->toMoney($amount);?> | <?php echo $engine->toMoney($agentprofit);?></div>
</div>


<div style="overflow: hidden; <?php echo $cl;?>">
<div style="float: left;"><strong style="color: black;"><?php echo $arrayuser[$rechargeproid];?></strong> || <?php echo $sync;?> <?php echo $transaction_status;?> | <?php echo $transaction_date;?></div>
<div style="float: right;"><?php echo $print;?></div>
</div>
</div>

<?php
	}if($engine->array_count($row) == 0){  echo "<div style='padding:5%; margin:5%; overflow:hidden; background-color:white;' class=''> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='shuziacolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}
?>
</div>