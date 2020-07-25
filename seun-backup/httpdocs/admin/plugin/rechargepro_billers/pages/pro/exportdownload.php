<?php
include "../../../../engine.autoloader.php";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=Downloadlist.csv");
header("Pragma: no-cache");
header("Expires: 0");







function myname($id,$engine){
$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));
return $row[0]['name'];
}





function raplace_string($string){
    $name = str_replace(array('"','\n','""','";"',"“"), array("'"," ","''","';'","'"), $string);
   $name = preg_replace('/\<[^\<]+\>/g', $name);
    return $name;
}





$start = $_REQUEST['date1'];
$end = date('Y-m-d 23:59:59', strtotime('+ 0days', strtotime($_REQUEST['date2'])));




$fp = fopen("php://output", "w");
$line1 = "";
$comma1 = "";
$line1 .= $comma1 . $start.'"-"'.$end;
$comma1 = ",";

$line1 .= "\n";
fputs($fp, $line1);





// fetch a row and write the column names out to the file
$row1 = array("SN","Agent Name","Phone","Account","Biller Ref","Bank Ref","Amount","status","message","payment method","IP","Date");
$line1 = "";
$comma1 = "";
foreach($row1 as $name){
    $line1 .= $comma1 . '"' . strtoupper(str_replace('"', '""', $name)) . '"';
    $comma1 = ",";
}
$line1 .= "\n";
fputs($fp, $line1);








$sn = 0;
$row = $engine->db_query2("SELECT rechargepro_transaction_log.transactionid , rechargepro_transaction_log.account_meter, rechargepro_transaction_log.phone, rechargepro_transaction_log.rechargeproid,  rechargepro_transaction_log.transaction_reference, rechargepro_transaction_log.bank_ref, rechargepro_transaction_log.amount, rechargepro_transaction_log.rechargepro_status, rechargepro_transaction_log.payment_method, rechargepro_transaction_log.ip, rechargepro_transaction_log.transaction_status, rechargepro_transaction_log.agent_id, rechargepro_transaction_log.transaction_date, rechargepro_transaction_log.rechargepro_status_code FROM rechargepro_transaction_log LEFT JOIN rechargepro_account ON rechargepro_transaction_log.rechargeproid = rechargepro_account.rechargeproid WHERE rechargepro_transaction_log.transaction_date BETWEEN ? AND ? ORDER BY rechargepro_transaction_log.transactionid DESC",array($start,$end));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $sn++;
   

    $rechargeproid = $row[$dbc]['rechargeproid']; 
    $phone = $row[$dbc]['phone']; 
    $transaction_reference = $row[$dbc]['transaction_reference']; 
    $bank_ref = $row[$dbc]['bank_ref']; 
    $amount = $row[$dbc]['amount']; 
    $status = $row[$dbc]['rechargepro_status']; 
    $payment_method = $row[$dbc]['payment_method']; 
    $ip = $row[$dbc]['ip']; 
    $transaction_status = $row[$dbc]['transaction_status']; 
    $rechargepro_status = $row[$dbc]['rechargepro_status']; 
    $transaction_date = $row[$dbc]['transaction_date'];
    $agent_id = myname($row[$dbc]['agent_id'],$engine);
    $rechargepro_status_code = $row[$dbc]['rechargepro_status_code'];
    $account_meter = $row[$dbc]['account_meter'];
    $transactionid = $row[$dbc]['transactionid'];
    
    
    $paidwith = "Pending";
    if($payment_method == "1"){ $paidwith = "Wallet";}
    if($payment_method == "2"){ $paidwith = "Card";}
    



$line = "";
$comma = "";


$line .= $comma.'"'.$sn.'",';
$line .= $comma.'"'.$agent_id.'",';
$line .= $comma.'"'.$phone.'",';
$line .= $comma.'"'.$account_meter.'",';
$line .= $comma.'"'.$transaction_reference.'",';
$line .= $comma.'"'.$bank_ref.'",';
$line .= $comma.'"'.$amount.'",';
$line .= $comma.'"'.$status.'",';
$line .= $comma.'"'.$transaction_status.'",';
$line .= $comma.'"'.$paidwith.'",';
$line .= $comma.'"'.$ip.'",';
$line .= $comma.'"'.$transaction_date.'",';


    $line .= "\n";
    fputs($fp, $line);
  
    


    }
    
    
    fclose($fp);
?>










