<?php
include "../../../../engine.autoloader.php";




if(isset($_REQUEST['buserid'])){
    
    $status = $_REQUEST['status'];
    $user = $_REQUEST['buserid'];
    
   $engine->db_query2("UPDATE rechargepro_account SET active =? WHERE rechargeproid = ? LIMIT 1",array($status,$user));
   
$row = $engine->db_query2("SELECT rechargeproid, name, email FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($user));
$name = $row[0]['name'];
$email = $row[0]['email'];

if($status == "1"){
$message =  "Dear $name, Unfortunately we have suspended your account. We are investigating the issue that caused this and will advise you shortly on next steps.<br />
Please reach out to us for any questions or if you feel this has been done in error.<br />
Thank you,<br />
rechargepro";
echo $engine->send_mail(array('noreply@rechargepro.com.ng','rechargepro!'),$email,"Your account has been suspended",$message);
 
}else{
$message =  "Hey $name, Welcome back to the rechargepro family! We are so happy your account has been reinstated.<br />
Please reach out to us for any questions you may have.<br />
Thank You,<br />
rechargepro";
echo $engine->send_mail(array('noreply@rechargepro.com.ng','rechargepro!'),$email,"Your account has been reinstated",$message);
 
}

  exit;    
}
?>