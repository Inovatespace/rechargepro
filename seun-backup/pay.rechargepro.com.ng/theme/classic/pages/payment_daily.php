<?php 
$engine = new engine();
if(!$engine->get_session("quickpayid")){ echo "<meta http-equiv='refresh' content='0;url=/signin&pp=".$engine->url_origin()."'>"; exit;};
$profile_creator = $engine->get_session("quickpayid");
$myproid = $engine->get_session("quickpayid");
$profile_role = $engine->get_session("quickpayrole");
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
if($engine->get_session("quickpayrole") < 3){
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


<script type="text/javascript">
jQuery(document).ready(function($){
var myCalendar;
myCalendar = new dhtmlXCalendarObject(["calendar1", "calendar2", "calendar3","calendar4","calendar5","calendar6","calendar7","calendar8","calendar9"]);
myCalendar.hideTime();
});
</script>


<div style="width:100%;">
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:5px 0px; overflow:hidden;">




<div class="profilebg" style="overflow:hidden; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Agent Account Manager</div></div>
<div class="shadow" style="margin-bottom: 10px; padding:10px 20px; overflow:hidden; background-color:white;">


<div style="float: left; margin-left:10px;">
<form method="post">
<input name="post" type="hidden" value="1" />
<select class="input" name="profile_creator" style="float: left; padding:5px;">
<option value="0">ALL</option>
<?php
$row = $engine->db_query("SELECT quickpayid,name FROM quickpay_account WHERE quickpay_cordinator = ? OR quickpayid = ? OR profile_creator = ?",array($engine->get_session("quickpayid"),$engine->get_session("quickpayid"),$engine->get_session("quickpayid")));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $quickpayid = $row[$dbc]['quickpayid']; 
    $name = $row[$dbc]['name']; 
?>
<option value="<?php echo $quickpayid;?>"><?php echo $name;?></option>
<?php	}?>
</select>
<input style="margin-left:5px; float: left; padding:5px;" type="input" id="calendar3" class="input" name="today" value="<?php echo $today;?>" />
<input style="margin-left:5px; float: left; padding:5px;" type="input" id="calendar2" class="input" name="later" value="<?php echo $later;?>" />
<input type="submit" value="View Transaction" style="margin-left:5px; float: left; color:white; border:none; padding:5px 10px; margin-right:5px;" class="mainbg shadow"/>
</div>
</form>
<a href="/agent"><button style="cursor:pointer; float: right; border: none; padding:5px 10px; margin:3px;" class="greenmenu shadow" ><span class="fas fa-user-tie"></span> Manage Agent</button></a>
	
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
<input type="text" style="width: 200px; float: left;" id="email" placeholder="Enter Email"/>
<input type="submit" value="Send Email" onclick="send_email()" style="margin:5px; margin-top:0px; float: left; color:white; border:none; padding:3px 10px;" class="mainbg shadow"/>
</div>

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
$allmyagent = array();
    $row = $engine->db_query("SELECT quickpayid FROM quickpay_account WHERE profile_creator = ?",array($profile_creator));
    for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $allmyagent[$row[$dbc]['quickpayid']] = $row[$dbc]['quickpayid'];
    }
    
    
function merchant($id,$engine){
 $row = $engine->db_query("SELECT name,	ac_ballance, profit_bal FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($id));
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


$row = $engine->db_query("SELECT account_meter,quickpay_service_charge,service_charge, quickpayid, amount, transaction_date, quickpay_subservice,agentprofit FROM quickpay_transaction_log WHERE transaction_date BETWEEN ? AND ? AND quickpay_status = ?  AND refund = '0' AND  (agent_id = ? ||  quickpayid = ?) ORDER BY transaction_date ASC",array($today,$later,"PAID",$profile_creator,$profile_creator));
	

for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $profitb = 0;
    
    $quickpayid = $row[$dbc]['quickpayid']; 
    $allmyagent[$quickpayid]=$quickpayid;
    $service_charge = $row[$dbc]['service_charge'];//-
    $quickpay_service_charge =$row[$dbc]['quickpay_service_charge']; 
    $tcount = 1;
    $paid_amount = $row[$dbc]['amount'];
    $last_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
    $quickpay_subservice = $row[$dbc]['quickpay_subservice']; 
    $account_meter = $row[$dbc]['account_meter'];
    $agentprofit = $row[$dbc]['agentprofit'];
    
    $profit = ($service_charge-$quickpay_service_charge)+$agentprofit;
    
    
    if($quickpayid == $account_meter){
      if(in_array($quickpay_subservice,array("TRANSFER","TOPUP","WITHDRAW"))){
        $profit = 0;
        $paid_amount = 0;
        $agentprofit = 0;
        $service_charge =0;
        $quickpay_service_charge=0;
        }
    }
    
  
   
         //if key = me
 //if sento in array
 if($profile_creator == $quickpayid){
 if(in_array($account_meter,$allmyagent)){
    
    
    
    if(in_array($quickpay_subservice,array("TRANSFER","TOPUP","WITHDRAW"))){
        
    }else{
                /**
     * START
     */
    if(!array_key_exists($quickpayid,$cardlist)){
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$quickpayid][$quickpay_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
    }else{
        
       if(isset($cardlist[$quickpayid][$quickpay_subservice])){
       $nowamount =  $paid_amount+$cardlist[$quickpayid][$quickpay_subservice][0]+$service_charge;
       $nowamount_s =  $paid_amount+$cardlist[$quickpayid][$quickpay_subservice][6];
       $nowcount =  1+$cardlist[$quickpayid][$quickpay_subservice][1];
       $first_date_time =  $cardlist[$quickpayid][$quickpay_subservice][2];
       $profitb = $cardlist[$quickpayid][$quickpay_subservice][5] + $profit;
       $cardlist[$quickpayid][$quickpay_subservice] = array($nowamount,$nowcount,$first_date_time,$last_date_time,$service_charge,$profitb,$nowamount_s);
       }else{
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$quickpayid][$quickpay_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
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
    if(!array_key_exists($quickpayid,$cardlist)){
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$quickpayid][$quickpay_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
    }else{
        
       if(isset($cardlist[$quickpayid][$quickpay_subservice])){
       $nowamount =  $paid_amount+$cardlist[$quickpayid][$quickpay_subservice][0]+$service_charge;
       $nowamount_s =  $paid_amount+$cardlist[$quickpayid][$quickpay_subservice][6];
       $nowcount =  1+$cardlist[$quickpayid][$quickpay_subservice][1];
       $first_date_time =  $cardlist[$quickpayid][$quickpay_subservice][2];
       $profitb = $cardlist[$quickpayid][$quickpay_subservice][5] + $profit;
       $cardlist[$quickpayid][$quickpay_subservice] = array($nowamount,$nowcount,$first_date_time,$last_date_time,$service_charge,$profitb,$nowamount_s);
       }else{
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$quickpayid][$quickpay_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
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
    if(!array_key_exists($quickpayid,$cardlist)){
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$quickpayid][$quickpay_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
    }else{
        
       if(isset($cardlist[$quickpayid][$quickpay_subservice])){
       $nowamount =  $paid_amount+$cardlist[$quickpayid][$quickpay_subservice][0]+$service_charge;
       $nowamount_s =  $paid_amount+$cardlist[$quickpayid][$quickpay_subservice][6];
       $nowcount =  1+$cardlist[$quickpayid][$quickpay_subservice][1];
       $first_date_time =  $cardlist[$quickpayid][$quickpay_subservice][2];
       $profitb = $cardlist[$quickpayid][$quickpay_subservice][5] + $profit;
       $cardlist[$quickpayid][$quickpay_subservice] = array($nowamount,$nowcount,$first_date_time,$last_date_time,$service_charge,$profitb,$nowamount_s);
       }else{
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$quickpayid][$quickpay_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount);
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


















<div style="margin-top: 40px; margin-bottom: 50px; display: none;">
<table id="myTable2" class="tablesorter" style="font-size: 85%;">
<thead>
<tr>
<th>#</th>
<th>NAME</th>
<th>MTN/PROFIT</th>
<th>GLO/PROFIT</th>
<th>9MOBILE/PROFIT</th> 
<th>AIRTEL/PROFIT</th> 
<th>DSTV/PROFIT</th>
<th>GOTV/PROFIT</th>
<th>STARTIMES/PROFIT</th>
<th>POWER/PROFIT</th>
<th>OTHERS/PROFIT</th>  
<th>SALES/PROFIT</th>                                        
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
$row = $engine->db_query("SELECT services_key FROM quickpay_services WHERE services_category = '1'",array());
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
<td><?php echo $mtn;?>/<?php echo $mtnpft;?></td>
<td><?php echo $glo;?>/<?php echo $glopft;?></td>
<td><?php echo $etisalat;?>/<?php echo $etisalatpft;?></td>
<td><?php echo $airtel;?>/<?php echo $airtelpft;?></td>
<td><?php echo $dstv;?>/<?php echo $dstvpft;?></td>
<td><?php echo $gotv;?>/<?php echo $gotvpft;?></td>
<td><?php echo $startie;?>/<?php echo $startiepft;?></td>
<td><?php echo $power;?>/<?php echo $powerpft;?></td>
<td><?php echo $others;?>/<?php echo $otherspft;?></td>
<td style="font-weight: bold; color:green;"><?php echo $mytotal;?> / <?php echo $mytotalpft;?></td>
</tr>
<?php
	}
?>

<tr>
<td style="font-weight: bold; color:blue;">TOTAL</td>
<td>-</td>
<td style="font-weight: bold; color:blue;"><?php echo $tmtn;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tglo;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tetisalat;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tairtel;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tdstv;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tgotv;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tstartie;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tpower;?></td>
<td style="font-weight: bold; color:blue;"><?php echo $tothers;?></td>
<td style="font-weight: bold; color:green;"><?php echo formatDollars($tmytotal);?>/<?php echo formatDollars($tmytotalpft);?></td>
</tr>
</tbody>
</table>

</div>













</div></div>