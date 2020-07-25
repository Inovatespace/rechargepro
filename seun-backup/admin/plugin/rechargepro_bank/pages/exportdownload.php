<?php
include "../../../engine.autoloader.php";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=Downloadlist.csv");
header("Pragma: no-cache");
header("Expires: 0");








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
$row1 = array("SN","transaction type","amount","date Time","Ref","Account","status","Description");
$line1 = "";
$comma1 = "";
foreach($row1 as $name){
    $line1 .= $comma1 . '"' . strtoupper(str_replace('"', '""', $name)) . '"';
    $comma1 = ",";
}
$line1 .= "\n";
fputs($fp, $line1);








$sn = 0;
$row = $engine->db_query("SELECT id,transaction_type,refid,amount,naration,acnumber,status,date FROM bank_alert WHERE date BETWEEN ? AND ? ORDER BY id DESC",array($start,$end));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $sn++;
   

   $id = $row[$dbc]['id'];
   $transaction_type = $row[$dbc]['transaction_type']; 
   $refid = $row[$dbc]['refid']; 
   $amount = $row[$dbc]['amount']; 
   $naration = $row[$dbc]['naration']; 
   $acnumber = $row[$dbc]['acnumber']; 
   $status = $row[$dbc]['status']; 
   $date = $row[$dbc]['date'];
    
    
    $st = "";
    if($status == "1"){ $st = "SYNC";}
    



$line = "";
$comma = "";


$line .= $comma.'"'.$sn.'",';
$line .= $comma.'"'.$transaction_type.'",';
$line .= $comma.'"'.$amount.'",';
$line .= $comma.'"'.$date.'",';
$line .= $comma.'"'.$refid.'",';
$line .= $comma.'"'.$acnumber.'",';
$line .= $comma.'"'.$st.'",';
$line .= $comma.'"'.$naration.'",';


    $line .= "\n";
    fputs($fp, $line);
  
    


    }
    
    
    fclose($fp);
?>










