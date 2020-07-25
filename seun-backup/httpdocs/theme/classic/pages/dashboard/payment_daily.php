<?php 
$engine = new engine();

$profile_creator = $engine->get_session("recharge4id");
$myproid = $engine->get_session("recharge4id");
$profile_role = $engine->get_session("recharge4role");
$myprofile_role = $profile_role;

function formatDollars($dollars){
  //return '&#8358;'.sprintf('%0.2f', $dollars);
  
  $formatted = "&#8358;" . number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $dollars)), 2);
    return $dollars < 0 ? "({$formatted})" : "{$formatted}";
}


if($profile_role > 2){ echo "<meta http-equiv='refresh' content='0;url=/transactionlog'>"; exit;};

if(isset($_REQUEST['profile_creator'])){
    
    
if($_REQUEST['post'] == "1"){
    
if($_REQUEST['profile_creator'] != 0){
if($engine->get_session("recharge4role") < 3){
$profile_creator = $_REQUEST['profile_creator'];
$myprofile_role = 2;
}
}
}

}


if(isset($_REQUEST['today'])){
$today = $engine->safe_html($_REQUEST['today']);
$later = date('Y-m-d 23:59:59', strtotime('+0 days', strtotime($_REQUEST['later'])));
}else{
$today = date("Y-m-d"); 
$later = date('Y-m-d 23:59:59', strtotime('+0 days', strtotime($today)));   
}


?>

<link rel="stylesheet" href="/java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />
<script type="text/javascript" src="/java/sort/jquery.tablesorter.js"></script>


<script type="text/javascript">
jQuery(document).ready(function($){
var myCalendar;
myCalendar = new dhtmlXCalendarObject(["calendar1", "calendar2", "calendar3","calendar4","calendar5","calendar6","calendar7","calendar8","calendar9"]);
myCalendar.hideTime();
});
</script>


<div style="width:100%; background-color: white; overflow:hidden; min-height: 500px;">
<div style="padding:10px 20px; overflow:hidden;">



<div class="profilebg" style="overflow:hidden; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Agent Account Manager</div></div>
<div class="shadow" style="margin-bottom: 10px; padding:10px 20px; overflow:hidden; background-color:white;">


<div style="float: left; margin-left:10px;">
<form method="post">
<input name="post" type="hidden" value="1" />
<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<select class="input" name="profile_creator" style="padding:5px; height: 30px;">
<option value="0">ALL</option>
<?php
$row = $engine->db_query("SELECT recharge4id,name FROM recharge4_account WHERE recharge4_cordinator = ? OR recharge4id = ? OR profile_creator = ?",array($engine->get_session("recharge4id"),$engine->get_session("recharge4id"),$engine->get_session("recharge4id")));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $recharge4id = $row[$dbc]['recharge4id']; 
    $name = $row[$dbc]['name']; 
?>
<option value="<?php echo $recharge4id;?>"><?php echo $name;?></option>
<?php	}?>
</select><span class="focus-border"><i></i></span>
</div>

<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<input class="input" style="padding:0px 5px; height: 30px;" type="input" id="calendar3" name="today" value="<?php echo $today;?>" /><span class="focus-border"><i></i></span>
</div>

<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<input class="input" style="padding:0px 5px; height: 30px;" type="input" id="calendar2" name="later" value="<?php echo $later;?>" /><span class="focus-border"><i></i></span>
</div>

<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<input type="submit" value="View Transaction" style="color:white; border:none; padding:5px 10px;  height: 30px;" class="mainbg shadow"/><span class="focus-border"><i></i></span>
</div>
</div>
</form>

	
</div>

<script type="text/javascript">
function send_email(){
    
    if(empty($("#email").val())){
      $.alert("Email Required"); return false;  
    }
    
                $.ajax({
                type: "POST",
                url: "/admin/cronjob/pdf.php",
                data: "email="+$("#email").val()+"&id=<?php echo $profile_creator;?>&start="+$("#calendar3").val()+"&end="+$("#calendar2").val(),
                cache: false,
                success: function (html) {
                   $.alert("Email Sent");
                   $("#email").val("");

                }


            });
            
}
</script>
<div class="profilebg" style="padding: 5px; font-weight: bold; color:black;"><?php echo $today;?> - <?php echo $later;?></div>


<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />
<script type="text/javascript" src="java/sort/jquery.tablesorter.js"></script>
<script type="text/javascript">
//$(document).ready(function(){$("#myTable").tablesorter();});
</script>

<div style="overflow: hidden;">
<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<input type="text" class="input" style="width: 200px; padding: 5px; height:30px;" id="email" placeholder="Enter Email"/><span class="focus-border"><i></i></span>
</div>

<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<input type="submit" value="Send Email" onclick="send_email()" style="color:white; border:none; padding:5px 10px; height:30px;" class="mainbg shadow"/><span class="focus-border"><i></i></span>
</div>
</div>
<style type="text/css">
@media print {
  * {
    display: none;
  }
  
    #printableTable {
    display: block;
    }
    }
</style>
<div style="text-align: right; font-size: 20px; margin-bottom:10px;"><div  style="cursor: pointer;"  onclick="printDiv('printableTable');" class="fas fa-print activemenu">PRINT</div></div>
  
<div id="printableTable">
<style type="text/css">
@media print {

@page {
  size: A4 landscape;
}

/* Size in mm */    
@page {
  size: 100mm 200mm landscape;
}

/* Size in inches */    
@page {
  size: 4in 6in landscape;
}


  table.tablesorter {
	width: 100%;
	text-align: left;
    border-collapse: collapse;
}
  /* tables */
.tablesorter td {
    padding: 3px;
    border:solid 1px #525151;
}
.tablesorter th {
     border:solid 1px #525151;
}
 
}
</style>
<table id="myTable" class="tablesorter" style="font-size: 85%;">
<thead>
<tr>
<th>#</th>
<th>NAME</th>
<th>FIRST TRANSACTION</th>
<th>LAST TRANSACTION</th>
<th>TOTAL AMOUNT</th> 
<th>SERVICE CHARGE</th> 
<th>TRANSACTION COUNT</th>
<th>TOPUP</th>
<th>PROFIT BALANCE</th>
<th>AC BALANCE</th> 
<th>REWARD</th>  
<th>TOTAL CASH</th>                                        
</tr>
</thead>
<tbody>
<?php
$agentcash = array();
$allmyagent = array();
$closingbalance = 0;
    $row = $engine->db_query("SELECT recharge4id,ac_ballance,profit_bal FROM recharge4_account WHERE profile_creator = ?",array($profile_creator));
    for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $allmyagent[$row[$dbc]['recharge4id']] = $row[$dbc]['recharge4id'];
    
    $closingbalance =  $closingbalance+($row[$dbc]['ac_ballance']+$row[$dbc]['profit_bal']);
    }
    
    
function merchant($id,$engine){
 $row = $engine->db_query("SELECT name,	ac_ballance, profit_bal FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($id));
 return array($row[0]['name'],$row[0]['ac_ballance'],$row[0]['profit_bal']);
}

$totalservicechargee = 0;
$totalprofit = 0;
$totalballance = 0;
$totaltopup = 0;
$totalcardbp = 0;
$totalcardb = 0;
$totalcount = 0;
$totalreward = 0;
$totalexpected = 0;
$totalballb = 0;
$totalsale = 0;
$totaltransfer = 0;
$sn = 0; 
$cardlist = array();


$row = $engine->db_query("SELECT account_meter,recharge4_service_charge,service_charge, recharge4id, amount, transaction_date, recharge4_subservice,agentprofit FROM recharge4_transaction_log WHERE transaction_date BETWEEN ? AND ? AND recharge4_status = ?  AND refund = '0' AND  (agent_id = ? ||  recharge4id = ?) ORDER BY transaction_date ASC",array($today,$later,"PAID",$profile_creator,$profile_creator));
	

for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $profitb = 0;
    
    $recharge4id = $row[$dbc]['recharge4id']; 
    $allmyagent[$recharge4id]=$recharge4id;
    $service_charge = $row[$dbc]['service_charge'];//-
    $recharge4_service_charge =$row[$dbc]['recharge4_service_charge']; 
    $tcount = 1;
    $paid_amount = $row[$dbc]['amount'];
    $last_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
    $recharge4_subservice = $row[$dbc]['recharge4_subservice']; 
    $account_meter = $row[$dbc]['account_meter'];
    $agentprofit = $row[$dbc]['agentprofit'];
    
    $profit = ($service_charge-$recharge4_service_charge)+$agentprofit;
    
    
    if($recharge4id == $account_meter){
      if(in_array($recharge4_subservice,array("TRANSFER","TOPUP","WITHDRAW"))){
        $profit = 0;
        $paid_amount = 0;
        $agentprofit = 0;
        $service_charge =0;
        $recharge4_service_charge=0;
        }
    }
    
  
   
         //if key = me
 //if sento in array
 if($profile_creator == $recharge4id){
 if(in_array($account_meter,$allmyagent)){
    
    
    
    if(in_array($recharge4_subservice,array("TRANSFER","TOPUP","WITHDRAW"))){
        
    }else{
                /**
     * START
     */
    if(!array_key_exists($recharge4id,$cardlist)){
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$recharge4id][$recharge4_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
    }else{
        
       if(isset($cardlist[$recharge4id][$recharge4_subservice])){
       $nowamount =  $paid_amount+$cardlist[$recharge4id][$recharge4_subservice][0]+$service_charge;
       $nowamount_s =  $paid_amount+$cardlist[$recharge4id][$recharge4_subservice][6];
       $nowcount =  1+$cardlist[$recharge4id][$recharge4_subservice][1];
       $first_date_time =  $cardlist[$recharge4id][$recharge4_subservice][2];
       $profitb = $cardlist[$recharge4id][$recharge4_subservice][5] + $profit;
       $myservicecharge = $cardlist[$recharge4id][$recharge4_subservice][4]+$service_charge;
       $cardlist[$recharge4id][$recharge4_subservice] = array($nowamount,$nowcount,$first_date_time,$last_date_time,$myservicecharge,$profitb,$nowamount_s);
       }else{
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$recharge4id][$recharge4_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
       }
    }
    /**
     * END
     */
    }
    
    


   }else{
    
        /**
     * START
     */
    if(!array_key_exists($recharge4id,$cardlist)){
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$recharge4id][$recharge4_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
    }else{
        
       if(isset($cardlist[$recharge4id][$recharge4_subservice])){
       $nowamount =  $paid_amount+$cardlist[$recharge4id][$recharge4_subservice][0]+$service_charge;
       $nowamount_s =  $paid_amount+$cardlist[$recharge4id][$recharge4_subservice][6];
       $nowcount =  1+$cardlist[$recharge4id][$recharge4_subservice][1];
       $first_date_time =  $cardlist[$recharge4id][$recharge4_subservice][2];
       $profitb = $cardlist[$recharge4id][$recharge4_subservice][5] + $profit;
       $myservicecharge = $cardlist[$recharge4id][$recharge4_subservice][4]+$service_charge;
       $cardlist[$recharge4id][$recharge4_subservice] = array($nowamount,$nowcount,$first_date_time,$last_date_time,$myservicecharge,$profitb,$nowamount_s);
       }else{
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$recharge4id][$recharge4_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
       }
    }
    /**
     * END
     */
    
   }
   
 }else{
     /**
     * START
     */
    if(!array_key_exists($recharge4id,$cardlist)){
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$recharge4id][$recharge4_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
    }else{
        
       if(isset($cardlist[$recharge4id][$recharge4_subservice])){
       $nowamount =  $paid_amount+$cardlist[$recharge4id][$recharge4_subservice][0]+$service_charge;
       $nowamount_s =  $paid_amount+$cardlist[$recharge4id][$recharge4_subservice][6];
       $nowcount =  1+$cardlist[$recharge4id][$recharge4_subservice][1];
       $first_date_time =  $cardlist[$recharge4id][$recharge4_subservice][2];
       $profitb = $cardlist[$recharge4id][$recharge4_subservice][5] + $profit;
       $myservicecharge = $cardlist[$recharge4id][$recharge4_subservice][4]+$service_charge;
       $cardlist[$recharge4id][$recharge4_subservice] = array($nowamount,$nowcount,$first_date_time,$last_date_time,$myservicecharge,$profitb,$nowamount_s);
       }else{
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$recharge4id][$recharge4_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
       }
    }
    /**
     * END
     */   
 }
    
    
    

  
    
}



$pft = "_pft";
$agent_array = array();
$service_array = array();
$userservice = array();
$badservice = array("BANK WITHDRAWAL","Credit","loan Credit","PROFIT","REWARD","TOPUP","Debit","loan Debit","WITHDRAW");//,"TRANSFER"
foreach($cardlist AS $key => $value){
$sn++;


$firsttransaction = "-";
$lasttransaction = "-";
$amount = 0;
$tcount = 0;
$topup = 0;
$transfer = 0;
$widrawl = 0;
$reward = 0;
$expected = 0;
$debit = 0;
$servicechargee = 0;

$mycount = 0;
foreach($value AS $service => $valuearray){
    
 if(!in_array($service,$badservice)){
    ////////////////////
    $service_array[$service] = $service;
    
    if(isset($userservice[$key])){
        
        if(isset($userservice[$key][$service])){
            $userservice[$key][$service] = $userservice[$key][$service] + $valuearray[6];//+ $valuearray[4]
            $userservice[$key][$service.$pft] = $userservice[$key][$service.$pft] + $valuearray[5];
        }else{
            $userservice[$key][$service] = $valuearray[6];//+ $valuearray[4]
            $userservice[$key][$service.$pft] = $valuearray[5];
        }
        
    }else{
      $sc = $valuearray[6];//+ $valuearray[4]
      $userservice[$key] = array($service=>$sc,$service.$pft=>$valuearray[5]);  
    }
    /////////////////////////////////
  
  $mycount++;
  if($mycount == 1){ $firsttransaction = $valuearray[2]; }
  $lasttransaction = $valuearray[3];;
  
  $amount = $amount+$valuearray[6];
  $servicechargee = $servicechargee+$valuearray[4];
  $tcount = $tcount+$valuearray[1];
  
  
 }

 
 if(in_array($service,array("PROFIT","REWARD"))){$reward = $reward+$valuearray[0]+$valuearray[4];}
    
    
 if(in_array($service,array("Credit","TOPUP","loan Credit"))){$topup = $topup + $valuearray[0]+$valuearray[4];}  
 
 if(in_array($service,array("Debit","loan Debit"))){$debit = $debit+$valuearray[0]+$valuearray[4];} 
 
 //if($service == "TRANSFER"){$transfer = $transfer+$valuearray[0];} 
 if($service == "WITHDRAW"){$widrawl = $widrawl+$valuearray[0]+$valuearray[4];} 
 
 
}


$topup = $topup - $widrawl;//$debit;
$transfer = $transfer;//-$widrawl;


$mc = merchant($key,$engine);
$name = "-";
$mb = 0;
$mp = 0;
if(count($mc) > 0){
    
   $name = $mc[0];
   $mb = $mc[1];
   $mp = $mc[2];
   
   $agent_array[$key] = $name;
}


$expected = $amount+$transfer;

$totalsale = $totalsale+$amount;
$totalcount = $totalcount+$tcount;
$totaltopup = $totaltopup + $topup;
$totaltransfer = $totaltransfer + $transfer;
$totalreward = $totalreward +$reward;
$totalservicechargee = $totalservicechargee+$servicechargee;
$totalballance = $totalballance+$mb;
$totalprofit = $totalprofit+$mp;
$totalexpected = $totalexpected+$expected;

$agentcash[$key] = ($expected+$servicechargee);
?>

<tr>
<td><?php echo $sn;?></td>
<td><?php echo $name;?></td>
<td><?php echo $firsttransaction;?></td>
<td><?php echo $lasttransaction;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $amount;?></td>
<td><?php echo $servicechargee;?></td>
<td><?php echo $tcount;?></td>
<td><?php echo $topup;?></td>
<td><?php echo $mp;?></td>
<td><?php echo $mb;?></td>
<td><?php echo $reward;?></td>
<td style="font-weight: bold; color:green;"><?php echo $expected+$servicechargee;?> <a href="/history&ac=<?php echo $key;?>"><span style="cursor: pointer;" class="fa fa-eye"></span></a></td>
</tr>
<?php
	}
?>

<tr style="font-weight: bold;">
<td>-</td>
<td>-</td>
<td>-</td>
<td>TOTAL</td>
<td style="font-weight: bold; color:blue;"><?php echo $totalsale;?></td>
<td><?php echo $totalservicechargee;?></td>
<td><?php echo $totalcount;?></td>
<td><?php echo "-";//$totaltopup;?></td>
<td style="font-weight: bold; color:red;"><?php echo $totalprofit;?></td>
<td><?php echo $totalballance;?></td>
<td><?php echo $totalreward;?></td>
<td style="font-weight: bold; color:green; font-size: 170%;"><?php echo formatDollars($totalexpected+$totalservicechargee);?></td>
</tr>

</tbody>
</table>

</div>
















<div style="margin-top: 40px; margin-bottom: 50px;">
<div style="text-align: right; font-size: 20px; margin-bottom:10px;"><div  style="cursor: pointer;"  onclick="printDiv('printableTable2');" class="fas fa-print activemenu">PRINT</div></div>
  
<div id="printableTable2">
<style type="text/css">
@media print {

@page {
  size: A4 landscape;
}

/* Size in mm */    
@page {
  size: 100mm 200mm landscape;
}

/* Size in inches */    
@page {
  size: 4in 6in landscape;
}


  table.tablesorter {
	width: 100%;
	text-align: left;
    border-collapse: collapse;
}
  /* tables */
.tablesorter td {
    padding: 3px;
    border:solid 1px #525151;
}
.tablesorter th {
     border:solid 1px #525151;
}
 
}
</style>

<table id="myTable2" class="tablesorter" style="font-size: 85%;">
<thead>
<tr>
<th>#</th>
<th>NAME</th>
<th>AIRTIME/PROFIT</th>
<th>TV/PROFIT</th>
<th>POWER/PROFIT</th>
<th>OTHERS/PROFIT</th>  
<th>TOTAL VALUE FROM WALLET</th>     
<th>TOTAL PROFIT</th>    
<th>TOTAL CASH</th>                                        
</tr>
</thead>
<tbody>


<?php
$tmtn = 0;
$tglo = 0;
$tetisalat = 0;
$tairtel = 0;
$tdstv = 0;
$tgotv = 0;
$tstartie = 0;
$tpower = 0;
$tothers = 0;
$tmytotal = 0;
$mytotalpft = 0;
$tmytotalpft = 0;
$tmparray = array("2353"=>"2353","ALC"=>"ALC","2354"=>"2354","ADC"=>"ADC","2352"=>"2352","AEC"=>"AEC","2351"=>"2351","ACC"=>"ACC","AQA"=>"AQA","AQC"=>"AQC","AWA"=>"AWA");
$powerkeys = array();
$row = $engine->db_query("SELECT services_key FROM recharge4_services WHERE services_category = '1'",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $powerkeys[$row[$dbc]['services_key']] = $row[$dbc]['services_key'];
    }
    
    $total_array = $tmparray+$powerkeys;

$sn = 0;
	foreach($userservice AS $key => $val){
	   $sn++;
       $mtn = 0; 
       $mtnpft = 0;
       if(isset($userservice[$key]['2353'])){$mtn = $mtn+$userservice[$key]['2353']; $mtnpft = $mtnpft+$userservice[$key]['2353_pft'];}
       if(isset($userservice[$key]['ALC'])){$mtn = $mtn+$userservice[$key]['ALC']; $mtnpft = $mtnpft+$userservice[$key]['ALC_pft'];}
       
       $glo = 0;
       $glopft = 0;
       if(isset($userservice[$key]['2354'])){$glo = $glo+$userservice[$key]['2354']; $glopft = $glopft+$userservice[$key]['2354_pft'];}
       if(isset($userservice[$key]['ADC'])){$glo = $glo+$userservice[$key]['ADC']; $glopft = $glopft+$userservice[$key]['ADC_pft'];}
       
       $etisalat = 0;
       $etisalatpft =0;
       if(isset($userservice[$key]['2352'])){$etisalat = $etisalat+$userservice[$key]['2352']; $etisalatpft = $etisalatpft+$userservice[$key]['2352_pft'];}
       if(isset($userservice[$key]['AEC'])){$etisalat = $etisalat+$userservice[$key]['AEC']; $etisalatpft = $etisalatpft+$userservice[$key]['AEC_pft'];}
       
       $airtel = 0;
        $airtelpft = 0;
       if(isset($userservice[$key]['2351'])){$airtel = $airtel+$userservice[$key]['2351']; $airtelpft = $airtelpft+$userservice[$key]['2351_pft'];}
       if(isset($userservice[$key]['ACC'])){$airtel = $airtel+$userservice[$key]['ACC']; $airtelpft = $airtelpft+$userservice[$key]['ACC_pft'];}
       
       $dstv = 0;
       $dstvpft =0;
       if(isset($userservice[$key]['AQA'])){$dstv = $dstv+$userservice[$key]['AQA'];$dstvpft = $dstvpft+$userservice[$key]['AQA_pft'];}
       $gotv = 0;
       $gotvpft =0;
       if(isset($userservice[$key]['AQC'])){$gotv = $gotv+$userservice[$key]['AQC'];$gotvpft = $gotvpft+$userservice[$key]['AQC_pft'];}
       $startie = 0;
       $startiepft =0;
       if(isset($userservice[$key]['AWA'])){$startie = $startie+$userservice[$key]['AWA'];$startiepft = $startiepft+$userservice[$key]['AWA_pft'];}
       
       
       
       $power = 0;
       $powerpft = 0;
      $result=array_intersect_key($powerkeys,$userservice[$key]);
      foreach($result AS $pk){
         $power = $power+$userservice[$key][$pk];
         $powerpft = $powerpft+$userservice[$key][$pk."_pft"];
      }
      
      $def = array_diff_key($userservice[$key],$total_array);
      
      $others = 0;
      $otherspft = 0;
      foreach($def AS $pka=>$pkb){
        if (strpos($pka, '_pft') == false) {
         $others = $others+$userservice[$key][$pka];
         $otherspft = $otherspft+$userservice[$key][$pka."_pft"];
         }
         
      }
      
      $mytotal = $mtn+$glo+$etisalat+$airtel+$dstv+$gotv+$startie+$power+$others;
      $mytotalpft = $mtnpft+$glopft+$etisalatpft+$airtelpft+$dstvpft+$gotvpft+$startiepft+$powerpft+$otherspft;
      
      
$tmtn = $tmtn+$mtn;
$tglo = $tglo+$glo;
$tetisalat = $tetisalat+$etisalat;
$tairtel = $tairtel+$airtel;
$tdstv = $tdstv+$dstv;
$tgotv = $tgotv+$gotv;
$tstartie = $tstartie+$startie;
$tpower = $tpower+$power;
$tothers = $tothers+$others;
$tmytotal = $tmytotal+$mytotal;

$tmytotalpft = $tmytotalpft+$mytotalpft;
?>
<tr>
<td><?php echo $sn;?></td>
<td><?php echo $agent_array[$key];?></td>
<td><?php echo ($mtn+$glo+$etisalat+$airtel);?>/<?php echo ($mtnpft+$glopft+$etisalatpft+$airtelpft);?></td>
<td><?php echo ($dstv+$gotv+$startie);?>/<?php echo ($dstvpft+$gotvpft+$startiepft);?></td>
<td><?php echo $power;?>/<?php echo $powerpft;?></td>
<td><?php echo $others;?>/<?php echo $otherspft;?></td>
<td style="font-weight: bold; color:green;"><?php echo $mytotal;?></td>
<td style="font-weight: bold; color:green;"><?php echo $mytotalpft;?></td>
<td style="font-weight: bold; color:green;"><?php echo $agentcash[$key];?></td>
</tr>
<?php
	}
?>

<tr>
<td style="font-weight: bold; color:blue;">TOTAL</td>
<td>-</td>
<td style="font-weight: bold; color:blue;"><?php echo ($tmtn+$tglo+$tetisalat+$tairtel);?></td>
<td style="font-weight: bold; color:blue;"><?php echo ($tdstv+$tgotv+$tstartie);?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tpower;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tothers;?></td>
<td style="font-weight: bold; color:green;"><?php echo formatDollars($tmytotal);?></td>
<td style="font-weight: bold; color:green;"><?php echo formatDollars($tmytotalpft);?></td>
<td style="font-weight: bold; color:green;"><?php echo formatDollars(array_sum($agentcash));?></td>
</tr>
</tbody>
</table>



<?php
$row = $engine->db_query("SELECT ac_ballance,profit_bal FROM recharge4_account WHERE recharge4id = ?",array($profile_creator));
$mybal = $row[0]['ac_ballance']+$row[0]['profit_bal'];
?>

<div style="margin-top:20px; font-weight:bold;">
<div style="margin-bottom: 10px;">Closing balance : <?php echo formatDollars(($closingbalance+$mybal));?></div>
<div style="margin-bottom: 10px;">Total cash received : <?php echo formatDollars($totalexpected+$totalservicechargee);?></div>
<div style="margin-bottom: 10px;">Total value debited from wallet : <?php echo formatDollars($tmytotal);?></div>
<div style="margin-bottom: 10px;">Total Profit : <?php echo formatDollars($tmytotalpft);?></div>
</div>
</div>
</div>






</div></div>




<script type="text/javascript">
function isChrome() {
  var isChromium = window.chrome,
    winNav = window.navigator,
    vendorName = winNav.vendor,
    isOpera = winNav.userAgent.indexOf("OPR") > -1,
    isIEedge = winNav.userAgent.indexOf("Edge") > -1,
    isIOSChrome = winNav.userAgent.match("CriOS");

  if (isIOSChrome) {
    return true;
  } else if (
    isChromium !== null &&
    typeof isChromium !== "undefined" &&
    vendorName === "Google Inc." &&
    isOpera === false &&
    isIEedge === false
  ) {
    return true;
  } else { 
    return false;
  }
}

function Print(elementId)
{
data = $("#"+elementId).html();
    var mywindow = window.open();
    var is_chrome = Boolean(mywindow.chrome);
    mywindow.document.write(data);

   if (is_chrome) {
     setTimeout(function() { // wait until all resources loaded 
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        mywindow.print(); // change window to winPrint
        mywindow.close(); // change window to winPrint
     }, 250);
   } else {
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();
   }

    return true;
}

jQuery(document).ready(function($){
//$(function () {
//setTimeout(Print('printDivContent'),10);
})

</script>
<script type="text/javascript">
      function printDiv(Id) {
         window.frames["print_frame"].document.body.innerHTML = document.getElementById(Id).innerHTML;
         window.frames["print_frame"].window.focus();
         window.frames["print_frame"].window.print();
       }
</script>

<iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>