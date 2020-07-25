<?php
//============================================================+
// File name   : example_048.php
// Begin       : 2009-03-20
// Last Update : 2013-05-14
//
// Description : Example 048 for TCPDF class
//               HTML tables and table headers
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+


require "../../engine.autoloader.php";

$today = date('Y-m-d', strtotime('-1 days', strtotime(date("Y-m-d"))));
$later = date('Y-m-d 23:59:59', strtotime('+0 days', strtotime($today)));
$date = $today." - ".$later;

$nowdate = date("Y-m-d");

$row = $engine->db_query("SELECT readid,reademail,rechargeproid FROM rechargepro_account_read WHERE datetime < ?",array($nowdate));
$emailto = $row[0]['reademail'];
$profile_creator = $row[0]['rechargeproid'];
$readid = $row[0]['readid'];


if(empty($profile_creator)){ exit;}
$setdate = date("Y-m-d H:i:s");
$engine->db_query("UPDATE rechargepro_account_read SET datetime = ? WHERE readid = ?",array($setdate,$readid));


/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: HTML tables and table headers
 * @author Nicola Asuni
 * @since 2009-03-20
 */

// Include the main TCPDF library (search for installation path).
// always load alternative config file for examples
require_once('pdf/config/tcpdf_config.php');

// Include the main TCPDF library (search the library on the following directories).
$tcpdf_include_dirs = array(
	realpath('pdf/tcpdf.php'),
	'/usr/share/php/tcpdf/tcpdf.php',
	'/usr/share/tcpdf/tcpdf.php',
	'/usr/share/php-tcpdf/tcpdf.php',
	'/var/www/tcpdf/tcpdf.php',
	'/var/www/html/tcpdf/tcpdf.php',
	'/usr/local/apache2/htdocs/tcpdf/tcpdf.php'
);
foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
	if (@file_exists($tcpdf_include_path)) {
		require_once($tcpdf_include_path);
		break;
	}
}

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('RechargePro');
$pdf->SetTitle('RechargePro Sales summary');
$pdf->SetSubject('RechargePro');
$pdf->SetKeywords('RechargePro');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'B', 20);

// add a page
//$pdf->AddPage();
$pdf->AddPage('L', 'A4');

//$pdf->Write(0, 'ssssssssssssss Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 8);


function formatDollars($dollars){
  //return '&#8358;'.sprintf('%0.2f', $dollars);
  
  $formatted = number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $dollars)), 2);
    return $dollars < 0 ? "({$formatted})" : "{$formatted}";
}

function merchant($id,$engine){
 $row = $engine->db_query("SELECT name,	ac_ballance, profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));
 return array($row[0]['name'],$row[0]['ac_ballance'],$row[0]['profit_bal']);
}





// -----------------------------------------------------------------------------


$allmyagent = array();
$closingbalance = 0;
$row = $engine->db_query("SELECT rechargeproid,ac_ballance,profit_bal FROM rechargepro_account WHERE profile_creator = ?",array($profile_creator));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$allmyagent[$row[$dbc]['rechargeproid']] = $row[$dbc]['rechargeproid'];
$closingbalance =  $closingbalance+($row[$dbc]['ac_ballance']+$row[$dbc]['profit_bal']);
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


$row = $engine->db_query("SELECT account_meter,rechargepro_service_charge,service_charge, rechargeproid, amount, transaction_date, rechargepro_subservice,agentprofit FROM rechargepro_transaction_log WHERE transaction_date BETWEEN ? AND ? AND rechargepro_status = ?  AND refund = '0' AND  (agent_id = ? ||  rechargeproid = ?) ORDER BY transaction_date ASC",array($today,$later,"PAID",$profile_creator,$profile_creator));


$tbl = '<table  border="1" cellpadding="2" cellspacing="2" nobr="true">
<thead>
<tr style="font-weight:bold;">
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
<tbody>';
$agentcash = array();
$rechargeproid = 0;
$salescount = $engine->array_count($row);
for($dbc = 0; $dbc < $salescount; $dbc++){
    $profitb = 0;
    $tmpservicecharge = 0;
    
    $rechargeproid = $row[$dbc]['rechargeproid']; 
    $allmyagent[$rechargeproid]=$rechargeproid;
    $service_charge = $row[$dbc]['service_charge'];//-
    $rechargepro_service_charge =$row[$dbc]['rechargepro_service_charge']; 
    $tcount = 1;
    $paid_amount = $row[$dbc]['amount'];
    $last_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
    $rechargepro_subservice = $row[$dbc]['rechargepro_subservice']; 
    $account_meter = $row[$dbc]['account_meter'];
    $agentprofit = $row[$dbc]['agentprofit'];
    
    $profit = ($service_charge-$rechargepro_service_charge)+$agentprofit;
    $realservicecharge = $service_charge;//-$rechargepro_service_charge;
    
    if($rechargeproid == $account_meter){
      if(in_array($rechargepro_subservice,array("TRANSFER","TOPUP","WITHDRAW"))){
        $profit = 0;
        $paid_amount = 0;
        $agentprofit = 0;
        $service_charge =0;
        $rechargepro_service_charge=0;
        $realservicecharge = 0;
        }
    }
    
  
   
         //if key = me
 //if sento in array
 if($profile_creator == $rechargeproid){
 if(in_array($account_meter,$allmyagent)){
    
    
    
    if(in_array($rechargepro_subservice,array("TRANSFER","TOPUP","WITHDRAW"))){
        
    }else{
                /**
     * START
     */
    if(!array_key_exists($rechargeproid,$cardlist)){
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$rechargeproid][$rechargepro_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount,$realservicecharge,$rechargepro_service_charge);
    }else{
        
       if(isset($cardlist[$rechargeproid][$rechargepro_subservice])){
       $nowamount =  $paid_amount+$cardlist[$rechargeproid][$rechargepro_subservice][0]+$service_charge;
       $nowamount_s =  $paid_amount+$cardlist[$rechargeproid][$rechargepro_subservice][6];
       $nowcount =  1+$cardlist[$rechargeproid][$rechargepro_subservice][1];
       $first_date_time =  $cardlist[$rechargeproid][$rechargepro_subservice][2];
       $profitb = $cardlist[$rechargeproid][$rechargepro_subservice][5] + $profit;
       $myservicecharge = $cardlist[$rechargeproid][$rechargepro_subservice][4]+$service_charge;
       $tmpservicecharge = $cardlist[$rechargeproid][$rechargepro_subservice][7]+$realservicecharge;
       $rpsc = $rechargepro_service_charge + $cardlist[$rechargeproid][$rechargepro_subservice][8];
       $cardlist[$rechargeproid][$rechargepro_subservice] = array($nowamount,$nowcount,$first_date_time,$last_date_time,$myservicecharge,$profitb,$nowamount_s,$tmpservicecharge,$rpsc);
       }else{
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$rechargeproid][$rechargepro_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount,$realservicecharge,$rechargepro_service_charge);
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
    if(!array_key_exists($rechargeproid,$cardlist)){
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$rechargeproid][$rechargepro_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount,$realservicecharge,$rechargepro_service_charge);
    }else{
        
       if(isset($cardlist[$rechargeproid][$rechargepro_subservice])){
       $nowamount =  $paid_amount+$cardlist[$rechargeproid][$rechargepro_subservice][0]+$service_charge;
       $nowamount_s =  $paid_amount+$cardlist[$rechargeproid][$rechargepro_subservice][6];
       $nowcount =  1+$cardlist[$rechargeproid][$rechargepro_subservice][1];
       $first_date_time =  $cardlist[$rechargeproid][$rechargepro_subservice][2];
       $profitb = $cardlist[$rechargeproid][$rechargepro_subservice][5] + $profit;
       $myservicecharge = $cardlist[$rechargeproid][$rechargepro_subservice][4]+$service_charge;
       $tmpservicecharge = $cardlist[$rechargeproid][$rechargepro_subservice][7]+$realservicecharge;
       $rpsc = $rechargepro_service_charge + $cardlist[$rechargeproid][$rechargepro_subservice][8];
       $cardlist[$rechargeproid][$rechargepro_subservice] = array($nowamount,$nowcount,$first_date_time,$last_date_time,$myservicecharge,$profitb,$nowamount_s,$tmpservicecharge,$rpsc);
       }else{
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$rechargeproid][$rechargepro_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount,$realservicecharge,$rechargepro_service_charge);
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
    if(!array_key_exists($rechargeproid,$cardlist)){
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$rechargeproid][$rechargepro_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount,$realservicecharge,$rechargepro_service_charge);
    }else{
        
       if(isset($cardlist[$rechargeproid][$rechargepro_subservice])){
       $nowamount =  $paid_amount+$cardlist[$rechargeproid][$rechargepro_subservice][0]+$service_charge;
       $nowamount_s =  $paid_amount+$cardlist[$rechargeproid][$rechargepro_subservice][6];
       $nowcount =  1+$cardlist[$rechargeproid][$rechargepro_subservice][1];
       $first_date_time =  $cardlist[$rechargeproid][$rechargepro_subservice][2];
       $profitb = $cardlist[$rechargeproid][$rechargepro_subservice][5] + $profit;
       $myservicecharge = $cardlist[$rechargeproid][$rechargepro_subservice][4]+$service_charge;
       $tmpservicecharge = $cardlist[$rechargeproid][$rechargepro_subservice][7]+$realservicecharge;
       $rpsc = $rechargepro_service_charge + $cardlist[$rechargeproid][$rechargepro_subservice][8];
       $cardlist[$rechargeproid][$rechargepro_subservice] = array($nowamount,$nowcount,$first_date_time,$last_date_time,$myservicecharge,$profitb,$nowamount_s,$tmpservicecharge,$rpsc);
       }else{
        $first_date_time = date("H:i:s A",  strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
        $cardlist[$rechargeproid][$rechargepro_subservice] = array($paid_amount,1,$first_date_time,$last_date_time,$service_charge,$profit,$paid_amount,$realservicecharge,$rechargepro_service_charge);
       }
    }
    /**
     * END
     */   
 }
    
    
    

  
    
}

if($salescount == 0){exit;}

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
            $userservice[$key][$service."_rpsc"] = $userservice[$key][$service."_rpsc"] + $valuearray[8];
            $userservice[$key][$service."service_charge"] = $userservice[$key][$service."service_charge"] + $valuearray[7];
        }else{
            $userservice[$key][$service] = $valuearray[6];//+ $valuearray[4]
            $userservice[$key][$service.$pft] = $valuearray[5];
            $userservice[$key][$service."_rpsc"] = $valuearray[8];
            $userservice[$key][$service."service_charge"] = $valuearray[7];
        }
        
    }else{
      $sc = $valuearray[6];//+ $valuearray[4]
      $userservice[$key] = array($service=>$sc,$service.$pft=>$valuearray[5]);  
      $userservice[$key][$service."service_charge"] = $valuearray[7];
      $userservice[$key][$service."_rpsc"] = $valuearray[8];
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


$tbl .= '
<tr>
<td>'.$sn.'</td>
<td>'.$name.'</td>
<td>'.$firsttransaction.'</td>
<td>'.$lasttransaction.'</td>
<td style="font-weight: bold; color:blue;">'.$amount.'</td>
<td>'.$servicechargee.'</td>
<td>'.$tcount.'</td>
<td>'.$topup.'</td>
<td>'.$mp.'</td>
<td>'.$mb.'</td>
<td>'.$reward.'</td>
<td style="font-weight: bold; color:green;">'.($expected+$servicechargee).'</td>
</tr>';

	}

$tbl .= '
<tr style="font-weight: bold;">
<td>-</td>
<td>-</td>
<td>-</td>
<td>TOTAL</td>
<td style="font-weight: bold; color:blue;">'.$totalsale.'</td>
<td>'.$totalservicechargee.'</td>
<td>'.$totalcount.'</td>
<td>-</td>
<td style="font-weight: bold; color:red;">'.$totalprofit.'</td>
<td>'.$totalballance.'</td>
<td style=" font-weight:bold;">'.$totalreward.'</td>
<td style="font-weight: bold; color:green; font-size: 130%; font-weight:bold;">'.formatDollars($totalexpected+$totalservicechargee).'</td>
</tr>

</tbody>
</table>
';

$pdf->SetFont('helvetica', 'B', 15);
$pdf->writeHTMLCell(0, 0, '', '', $date, 0, 1, 0, true, '', true);
$pdf->SetFont('helvetica', '', 8);

$html = <<<EOD
Summary 1
EOD;
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
$pdf->writeHTML($tbl, true, false, false, false, '');



//$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

$tbl = '<table  border="1" cellpadding="2" cellspacing="2" nobr="true">
<thead>
<tr style="font-weight:bold;">
<th>#</th>
<th>NAME</th>
<th>AIRTIME/PROFIT</th>
<th>TV/PROFIT</th>
<th>POWER/PROFIT</th>
<th>OTHERS/PROFIT</th>  
<th>TOTAL VALUE FROM WALLET</th>    
<th>SERVICE CHANGE</th>  
<th>TOTAL PROFIT</th>        
<th>TOTAL CASH</th>                                
</tr>
</thead>
<tbody>';

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
$tmytotalsc = 0;
$tmytotalpft = 0;
$tmparray = array("2353"=>"2353","ALC"=>"ALC","2354"=>"2354","ADC"=>"ADC","2352"=>"2352","AEC"=>"AEC","2351"=>"2351","ACC"=>"ACC","AQA"=>"AQA","AQC"=>"AQC","AWA"=>"AWA");
$powerkeys = array();
$row = $engine->db_query("SELECT services_key FROM rechargepro_services WHERE services_category = '1'",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $powerkeys[$row[$dbc]['services_key']] = $row[$dbc]['services_key'];
    }
    
    $total_array = $tmparray+$powerkeys;

$sn = 0;
	foreach($userservice AS $key => $val){
	   $sn++;
       $allservicecharge = 0;
       
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
       $rpsc = 0;
       if(isset($userservice[$key]['AQA'])){$dstv = $dstv+$userservice[$key]['AQA'];$dstvpft = $dstvpft+$userservice[$key]['AQA_pft']; $rpsc = $rpsc+$userservice[$key]['AQA_rpsc'];}
       if(isset($userservice[$key]["AQAservice_charge"])){$allservicecharge = $allservicecharge+$userservice[$key]["AQAservice_charge"];}
       $gotv = 0;
       $gotvpft =0;
       if(isset($userservice[$key]['AQC'])){$gotv = $gotv+$userservice[$key]['AQC'];$gotvpft = $gotvpft+$userservice[$key]['AQC_pft']; $rpsc = $rpsc+$userservice[$key]['AQC_rpsc'];}
       if(isset($userservice[$key]["AQCservice_charge"])){$allservicecharge = $allservicecharge+$userservice[$key]["AQCservice_charge"];}
       
       $startie = 0;
       $startiepft =0;
       if(isset($userservice[$key]['AWA'])){$startie = $startie+$userservice[$key]['AWA'];$startiepft = $startiepft+$userservice[$key]['AWA_pft']; $rpsc = $rpsc+$userservice[$key]['AWA_rpsc'];}
       if(isset($userservice[$key]["AWAservice_charge"])){$allservicecharge = $allservicecharge+$userservice[$key]["AWAservice_charge"];}
       
       
       
       $power = 0;
       $powerpft = 0;
      $result=array_intersect_key($powerkeys,$userservice[$key]);
      foreach($result AS $pk){
         $power = $power+$userservice[$key][$pk];
         $powerpft = $powerpft+$userservice[$key][$pk."_pft"];
         if(isset($userservice[$key][$pk."service_charge"])){$allservicecharge = $allservicecharge+$userservice[$key][$pk."service_charge"]; $rpsc = $rpsc+$userservice[$key][$pk.'_rpsc'];}
      }
      
      $def = array_diff_key($userservice[$key],$total_array);
      
      $others = 0;
      $otherspft = 0;
      foreach($def AS $pka=>$pkb){
        if (strpos($pka, '_pft') == false) {
            if (strpos($pka, 'service_charge') == false) {  
         $others = $others+$userservice[$key][$pka];
         if(isset($userservice[$key][$pka."_pft"])){
         $otherspft = $otherspft+$userservice[$key][$pka."_pft"];
         }
         if(isset($userservice[$key][$pka."service_charge"])){$allservicecharge = $allservicecharge+$userservice[$key][$pka."service_charge"]; $rpsc = $rpsc+$userservice[$key][$pka.'_rpsc'];}
         }
         }
         
      }
      
      $mytotal = ($mtn+$glo+$etisalat+$airtel+$dstv+$gotv+$startie+$power+$others)-$rpsc;
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
$tmytotalsc = $tmytotalsc+$allservicecharge;
$tmytotalpft = $tmytotalpft+$mytotalpft;

$tbl .= '
<tr>
<td>'.$sn.'</td>
<td>'.$agent_array[$key].'</td>
<td>'.($mtn+$glo+$etisalat+$airtel).'/'.($mtnpft+$glopft+$etisalatpft+$airtelpft).'</td>
<td>'.($dstv+$gotv+$startie).'/'.($dstvpft+$gotvpft+$startiepft).'</td>
<td>'.$power.'/'.$powerpft.'</td>
<td>'.$others.'/'.$otherspft.'</td>
<td style="font-weight: bold; color:green;">'.$mytotal.'</td>
<td style="font-weight: bold; color:green;">'.$allservicecharge.'</td>
<td style="font-weight: bold; color:green;">'.$mytotalpft.'</td>
<td style="font-weight: bold; color:green;">'.$agentcash[$key].'</td>
</tr>';

	}

$tbl .= '
<tr>
<td style="font-weight: bold; color:blue;">TOTAL</td>
<td>-</td>
<td style="font-weight: bold; color:blue;">'.($tmtn+$tglo+$tetisalat+$tairtel).'</td>
<td style="font-weight: bold; color:blue;">'.($tdstv+$tgotv+$tstartie).'</td>
<td style="font-weight: bold; color:blue;">'.$tpower.'</td>
<td style="font-weight: bold; color:blue;">'.$tothers.'</td>
<td style="font-weight: bold; color:green;">'.formatDollars($tmytotal).'</td>
<td style="font-weight: bold; color:green;">'.formatDollars($tmytotalsc).'</td>
<td style="font-weight: bold; color:green;">'.formatDollars($tmytotalpft).'</td>
<td style="font-weight: bold; color:green;">'.formatDollars(array_sum($agentcash)).'</td>
</tr>
</tbody>
</table>
';

$html = <<<EOD
Summary 2
EOD;
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
$pdf->writeHTML($tbl, true, false, false, false, '');



$row = $engine->db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ?",array($profile_creator));
$mybal = $row[0]['ac_ballance']+$row[0]['profit_bal'];
$closingbalance = $closingbalance+$mybal;

$todaya = date('Y-m-d', strtotime('-1 days', strtotime($today)));
$latera = date('Y-m-d 23:59:59', strtotime('+0 days', strtotime($todaya)));  
$row = $engine->db_query("SELECT bal2,transactionid FROM rechargepro_transaction_log WHERE cordinator_id = ? AND transaction_date BETWEEN ? AND ? ORDER BY transactionid DESC LIMIT 1",array($profile_creator,$todaya,$latera));
$opening = $row[0]['bal2'];


if($profile_creator == "11555"){
if($today == date('Y-m-d', strtotime('-1 days', strtotime(date("Y-m-d"))))){
    $myst = $tmytotalsc/100;
    $myst = $myst*75; 
   $myrealbal = $opening - $tmytotal + ($tmytotalpft - $myst);
   if($closingbalance != $myrealbal){
    $closingbalance = $myrealbal;
    $engine->db_query("UPDATE rechargepro_account SET ac_ballance = ?,profit_bal =? WHERE rechargeproid = ?",array($closingbalance,0,'115'));
    $engine->db_query("UPDATE rechargepro_transaction_log SET bal2 =?  WHERE agent_id = '115' OR rechargeproid = '115' ORDER BY transactionid DESC LIMIT 1",array($closingbalance));
   }
}
}

$content = '<div style="margin-bottom: 30px; font-weight:bold;">
Opening balance : N'.formatDollars($opening).'<br />
Closing balance : N'.formatDollars(($closingbalance)).'<br />
Total cash received : N'.formatDollars(($totalexpected+$totalservicechargee)).'<br />
Total value debited from wallet : N'.formatDollars($tmytotal).'<br />
Total Profit : N'.formatDollars($tmytotalpft).'
</div>';
$pdf->SetFont('helvetica', 'B', 10);
$pdf->writeHTMLCell(0, 0, '', '', $content, 0, 1, 0, true, '', true);



$row = $engine->db_query("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($profile_creator));
$name = $row[0]['name'];

$startdate = date('F d Y', strtotime('+0 days', strtotime($today)));
$enddate = date('F d Y 23:59 A', strtotime('+0 days', strtotime($later)));
           
$meshtml = '_____________________________________________________________________________________________________________________________________<br /><br />Dear '.$name.',<br />
Consolidated e-statement for the period '.$startdate.' to '.$enddate.'.<br />
Your e-statement is user-friendly with Consolidated view of transactions on your accounts.<br />
For enquiries, please call our  Contact Centre on 08181770906 10AM to 5PM[monday to friday], Watsapp 08181770906 24/7 or email to support@rechargepro.com.ng<br />
Thank you for choosing RechargePro.<br />
Yours faithfully,<br />
RechargePro<br />
https://rechargepro.com.ng';
$pdf->SetFont('helvetica', 'B', 8);
$pdf->writeHTMLCell(0, 0, '', '', $meshtml, 0, 1, 0, true, '', true);
         



//$pdf->writeHTML($tbl, true, false, false, false, '');
// -----------------------------------------------------------------------------

$filename = "sales".$rechargeproid.rand(00000000,99999999);
//Close and output PDF document
$pdf->Output(dirname(dirname(__file__)).'/tmp/'.$filename.'.pdf', 'F');


  $attachment = array(
    array(
                "path" =>dirname(dirname(__file__))."/tmp/".$filename.".pdf",
                "fileName" => "Sales Report.pdf", 
           )
           );
           



$mes = $tbl."<br /><br />";

$mes .= $content;

$mes .= 'Dear '.$name.',<br /><br />
Please find attached your consolidated e-statement for the period '.$startdate.' to '.$enddate.'.<br /><br />
Your e-statement is secure and user-friendly with Consolidated view of transactions on your accounts.<br /><br />
Open the attached statement.<br /><br />
For enquiries, please call our 24 hour Contact Centre on 08181770906 or email to support@rechargepro.com.ng<br /><br />
Thank you for choosing RechargePro.<br /><br />
Yours faithfully,<br /><br />
RechargePro<br />
https://rechargepro.com.ng<br /><br />';

    $engine->send_mail(array("noreply@rechargepro.com.ng","RechargePro"),$emailto,"Sales Report",$mes,$attachment);

//============================================================+
// END OF FILE
//============================================================+
