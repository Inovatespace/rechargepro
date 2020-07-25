<?php
include ('../engine.autoloader.php');




if (empty($_REQUEST['auth']) || empty($_REQUEST['username'])) {
    echo "bad*0";
    exit;
}

$auth = $_REQUEST['auth'];
$email = $_REQUEST['username'];



$row = $engine->db_query("SELECT email FROM quickpay_account WHERE email = ? || mobile = ? LIMIT 1",array($email,$email));
$email = $row[0]['email'];


$row = $engine->db_query("SELECT id FROM temp_code WHERE email = ? AND code = ? AND status = '0' LIMIT 1",array($email,$auth));
if(empty($row[0]['id'])){
     echo "bad*0";
    exit;  
}


$devicecount = $engine->db_query("SELECT id FROM quickpay_access WHERE email = ?",array($email),true);
if($devicecount > 2){
   echo "goo*"; exit; 
}


$threemonth = date("Y-m-d H:i:s", strtotime('-90 days', strtotime(date("Y-m-d H:i:s"))));
$engine->db_query("DELETE FROM temp_code WHERE date <= ? LIMIT 1", array($threemonth));  
$engine->db_query("UPDATE temp_code SET STATUS = '1' WHERE id = ? LIMIT 1", array($row[0]['id']));
       
//$engine->db_query("DELETE FROM temp_code WHERE id = ? LIMIT 1",array($row[0]['id']));

$engine->db_query("INSERT INTO quickpay_access (email,mac,name) VALUES (?,?,?)",array($email,"web","web"));  
 
echo "ok*";
exit;

?>