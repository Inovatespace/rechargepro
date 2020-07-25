<?php
include "../../../../engine.autoloader.php";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=userlist.csv");
header("Pragma: no-cache");
header("Expires: 0");
$id = $_REQUEST['id'];
























$fp = fopen("php://output", "w");
$line1 = "";
$comma1 = "";
$line1 .= $comma1 . '"-"';
$comma1 = ",";

$line1 .= "\n";
fputs($fp, $line1);





// fetch a row and write the column names out to the file
$row1 = array("SN","rechargeproid","title","name","sex","mobile","email","rechargeprorole","username","ac_ballance","active","auto_feed_cahier_account","profile_agent","profile_creator","bank_name","bank_ac_name","bank_ac_number","call_back_url","last_payout","created_date");
$line1 = "";
$comma1 = "";
foreach($row1 as $name){
    $line1 .= $comma1 . '"' . strtoupper(str_replace('"', '""', $name)) . '"';
    $comma1 = ",";
}
$line1 .= "\n";
fputs($fp, $line1);





    
    
    
    



$sn = 0;
 $row = $engine->db_query2("SELECT rechargeproid,title,name,sex,mobile,email,rechargeprorole,username,ac_ballance,active,auto_feed_cahier_account,profile_agent,profile_creator,bank_name,bank_ac_name,bank_ac_number,call_back_url,last_payout,created_date FROM rechargepro_account WHERE rechargeproid = ?",array($id)); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $sn++;
    
$rechargeproid = $row[$dbc]['rechargeproid']; 
$title = $row[$dbc]['title']; 
$name = $row[$dbc]['name']; 
$sex = $row[$dbc]['sex']; 
$mobile = $row[$dbc]['mobile']; 
$email = $row[$dbc]['email']; 
$rechargeprorole = $row[$dbc]['rechargeprorole']; 
$username = $row[$dbc]['username']; 
$ac_ballance = $row[$dbc]['ac_ballance']; 
$active = $row[$dbc]['active']; 
$auto_feed_cahier_account = $row[$dbc]['auto_feed_cahier_account']; 
$profile_agent = $row[$dbc]['profile_agent']; 
$profile_creator = $row[$dbc]['profile_creator']; 
$bank_name = $row[$dbc]['bank_name']; 
$bank_ac_name = $row[$dbc]['bank_ac_name']; 
$bank_ac_number = $row[$dbc]['bank_ac_number']; 
$call_back_url = $row[$dbc]['call_back_url']; 
$last_payout = $row[$dbc]['last_payout']; 
$created_date = $row[$dbc]['created_date'];



$line = "";
$comma = "";


$line .= $comma.'"'.$sn.'",';
$line .= $comma.'"'.$rechargeproid.'",';
$line .= $comma.'"'.$title.'",';
$line .= $comma.'"'.$name.'",';
$line .= $comma.'"'.$sex.'",';
$line .= $comma.'"'.$mobile.'",';
$line .= $comma.'"'.$email.'",';
$line .= $comma.'"'.$rechargeprorole.'",';
$line .= $comma.'"'.$username.'",';
$line .= $comma.'"'.$ac_ballance.'",';
$line .= $comma.'"'.$active.'",';
$line .= $comma.'"'.$auto_feed_cahier_account.'",';
$line .= $comma.'"'.$profile_agent.'",';
$line .= $comma.'"'.$profile_creator.'",';
$line .= $comma.'"'.$bank_name.'",';
$line .= $comma.'"'.$bank_ac_name.'",';
$line .= $comma.'"'.$bank_ac_number.'",';
$line .= $comma.'"'.$call_back_url.'",';
$line .= $comma.'"'.$last_payout.'",';
$line .= $comma.'"'.$created_date.'",';


    $line .= "\n";
    fputs($fp, $line);
  
    


    }
    
    
    fclose($fp);
?>










