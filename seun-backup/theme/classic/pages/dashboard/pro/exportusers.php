<?php
include "../../../../../engine.autoloader.php";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=userlist.csv");
header("Pragma: no-cache");
header("Expires: 0");











$fp = fopen("php://output", "w");
$line1 = "";
$comma1 = "";
$line1 .= $comma1 . '"-"';
$comma1 = ",";

$line1 .= "\n";
fputs($fp, $line1);





// fetch a row and write the column names out to the file
$row1 = array("SN","recharge4id","name","live_account","status","process_transaction","email","mobile","ballance","call_back_url","bank_name","bank_ac_name","bank_ac_number","last_payout","created_date");
$line1 = "";
$comma1 = "";
foreach($row1 as $name){
    $line1 .= $comma1 . '"' . strtoupper(str_replace('"', '""', $name)) . '"';
    $comma1 = ",";
}
$line1 .= "\n";
fputs($fp, $line1);








$sn = 0;
 $row = $engine->db_query("SELECT recharge4id,name,active,email,mobile,ac_ballance,call_back_url,bank_name,bank_ac_name,bank_ac_number,last_payout,created_date FROM recharge4_account WHERE profile_creator = ?",array($engine->get_session("adminid"))); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $sn++;
    
$line = "";
$comma = "";


$line .= $comma.'"'.$sn.'",';
$line .= $comma.'"'.$row[$dbc]['recharge4id'].'",';
$line .= $comma.'"'.$row[$dbc]['name'].'",';
//$line .= $comma.'"'.$row[$dbc]['profile_live_account'].'",';
$line .= $comma.'"'.$row[$dbc]['active'].'",';
//$line .= $comma.'"'.$row[$dbc]['profile_process_transaction'].'",';
$line .= $comma.'"'.$row[$dbc]['email'].'",';
$line .= $comma.'"'.$row[$dbc]['mobile'].'",';
$line .= $comma.'"'.$row[$dbc]['ac_ballance'].'",';
$line .= $comma.'"'.$row[$dbc]['call_back_url'].'",';
$line .= $comma.'"'.$row[$dbc]['bank_name'].'",';
$line .= $comma.'"'.$row[$dbc]['bank_ac_name'].'",';
$line .= $comma.'"'.$row[$dbc]['bank_ac_number'].'",';
$line .= $comma.'"'.$row[$dbc]['last_payout'].'",';
$line .= $comma.'"'.$row[$dbc]['created_date'].'",';




    $line .= "\n";
    fputs($fp, $line);
  
    


    }
    
    
    fclose($fp);
?>










