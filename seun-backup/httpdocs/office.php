<?php
include "engine.autoloader.php";

$start = date("2019-08-12");
$end = date('2019-08-18 23:59:59', strtotime('+0 day', strtotime(date("y-m-d")))); 




$mail = '<div style="font-size:25px; margin-bottom:10px;">End of week report '.date('F, d Y', strtotime('+0 day', strtotime($start))).' - '.date('F, d Y', strtotime('+0 day', strtotime($end))).'</div>';



$mail .= '<table   border="1" cellpadding="2" cellspacing="2" nobr="true" style="width: 100%;">
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
<tbody>';


$total_tid = 0;
$total_amount = 0;
$total_rechargeproprofit = 0;
$total_cordprofit = 0;
$total_agentprofit = 0;
$total_refererprofit = 0;


$array_special = array();
$array = array();
$chartarray = array();
$row = $engine->db_query("SELECT SUM(amount) amt, COUNT(transactionid) AS tid, rechargepro_service, rechargepro_subservice, SUM(refererprofit) AS rp, SUM(agentprofit) AS ap, SUM(cordprofit) AS cp, SUM(rechargeproprofit) as bp FROM rechargepro_transaction_log WHERE ((rechargepro_status = 'PAID' OR rechargepro_service = 'CREDIT' OR rechargepro_service = 'Debit') AND rechargepro_service != 'TRANSFER' AND rechargepro_service != 'PROFIT' AND rechargepro_service != 'REWARD' AND rechargepro_service != 'TOPUP' AND rechargepro_service != 'WITHDRAW' )  AND transaction_date BETWEEN ? AND ? GROUP BY  rechargepro_subservice",array($start,$end));
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
    
    
    if(!in_array($rechargepro_service,array("Credit","Debit","REFUND","AUTO TOPUP","Loan Payment","loan Credit","AUTO PAY"))){
    $array[] = $rechargepro_service;
   
    $chartarray[$rechargepro_service] = array($amount,$rechargeproprofit,$cordprofit,$agentprofit,$refererprofit);    
        
    $total_tid = $total_tid + $tid;
    $total_amount = $total_amount + $amount;
    $total_rechargeproprofit = $total_rechargeproprofit + $rechargeproprofit;
    $total_cordprofit = $total_cordprofit + $cordprofit;
    $total_agentprofit = $total_agentprofit + $agentprofit;
    $total_refererprofit = $total_refererprofit +$refererprofit;
    }
    

    
        if(!in_array($rechargepro_service,array("Credit","Debit","REFUND","AUTO TOPUP","Loan Payment","loan Credit","AUTO PAY"))){
   

$mail .='
<tr >
<td>'.$rechargepro_service.'</td>
<td>'.$tid.'</td>
<td>'.$engine->toMoney($amount,"&#8358;").'</td>
<td>'.$engine->toMoney($rechargeproprofit,"&#8358;").'</td>
<td>'.$engine->toMoney($cordprofit,"&#8358;").' </td>
<td>'.$engine->toMoney($agentprofit,"&#8358;").'</td>
<td>'.$engine->toMoney($refererprofit,"&#8358;").'</td>
</tr>';
}

	}
$mail .='
<tr style="font-weight: bold;" >
<td>TOTAL</td>
<td>'.$total_tid.'</td>
<td>'.$engine->toMoney($total_amount,"&#8358;").'</td>
<td>'.$engine->toMoney($total_rechargeproprofit,"&#8358;").'</td>
<td>'.$engine->toMoney($total_cordprofit,"&#8358;").' </td>
<td>'.$engine->toMoney($total_agentprofit,"&#8358;").'</td>
<td>'.$engine->toMoney($total_refererprofit,"&#8358;").'</td>
</tr>';



$mail .='</tbody>
</table>';


  $attachment = array();
   
     $engine->send_mail(array("noreply@rechargepro.com.ng","RechargePro"),"efa.imoke@vertistechnologiesltd.com","Financial Report $start - $end",$mail,$attachment);
      $engine->send_mail(array("noreply@rechargepro.com.ng","RechargePro"),"nebolisa@vertistechnologiesltd.com","Financial Report $start - $end",$mail,$attachment);
       $engine->send_mail(array("noreply@rechargepro.com.ng","RechargePro"),"seuntech2k@yahoo.com","Financial Report $start - $end",$mail,$attachment);
    
?>