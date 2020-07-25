<?php
include "../../../../engine.autoloader.php";

if(!$engine->get_session("adminid")){exit;}


if(isset($_REQUEST['smsid'])){
    
    $user = $_REQUEST['user'];
    $code = $engine->RandomString(2,6);
    
    if($_REQUEST['smsid'] == 0){
        $engine->db_query2("UPDATE rechargepro_account SET sms_activation = '' WHERE rechargeproid = ? LIMIT 1",array($user)); 
    }else{
      $engine->db_query2("UPDATE rechargepro_account SET sms_activation =? WHERE rechargeproid = ? LIMIT 1",array($code,$user));   
    }
    
    $engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("adminid"),"SMS STATUS",$_REQUEST['smsid']."_".$user));
     exit;
}



if(isset($_REQUEST['mergeid'])){
    
    $user = $_REQUEST['user'];
    
    if($_REQUEST['mergeid'] == 0){
        $engine->db_query2("UPDATE rechargepro_account SET merge_ac = '0' WHERE rechargeproid = ? LIMIT 1",array($user)); 
    }else{
      $engine->db_query2("UPDATE rechargepro_account SET merge_ac = '1' WHERE rechargeproid = ? LIMIT 1",array($user));   
    }
    
    $engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("adminid"),"SMS STATUS",$_REQUEST['mergeid']."_".$user));
     exit;
}


if(isset($_REQUEST['autofeedamount'])){
    
    $user = $_REQUEST['user'];
    $autofeedamount = $_REQUEST['autofeedamount'];
  
      $engine->db_query2("UPDATE rechargepro_account SET feed_cahier_account_amount =? WHERE rechargeproid = ? LIMIT 1",array($autofeedamount,$user));   
    
    
    $engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("adminid"),"AUTOFEED AMOUNT",$_REQUEST['autofeedamount']."_".$user));
     exit;
}


if(isset($_REQUEST['autofeed'])){
    
    $user = $_REQUEST['user'];
    
    if($_REQUEST['autofeed'] == 0){
        $engine->db_query2("UPDATE rechargepro_account SET auto_feed_cahier_account = '0' WHERE rechargeproid = ? LIMIT 1",array($user)); 
    }else{
      $engine->db_query2("UPDATE rechargepro_account SET auto_feed_cahier_account ='1' WHERE rechargeproid = ? LIMIT 1",array($user));   
    }
    
    $engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("adminid"),"AUTOFEED STATUS",$_REQUEST['autofeed']."_".$user));
     exit;
}


if(isset($_REQUEST['scharge'])){
    
    $user = $_REQUEST['user'];
    
    if($_REQUEST['scharge'] == 0){
        $engine->db_query2("UPDATE rechargepro_account SET is_service_charge = '0' WHERE rechargeproid = ? LIMIT 1",array($user)); 
    }else{
      $engine->db_query2("UPDATE rechargepro_account SET is_service_charge ='1' WHERE rechargeproid = ? LIMIT 1",array($user));   
    }
    
        $engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("adminid"),"SCHARGE STATUS",$_REQUEST['scharge']."_".$user));
     exit;
}
    

if(isset($_REQUEST['transferid'])){
    
    $user = $_REQUEST['user'];
    
    if($_REQUEST['transferid'] == 0){
        $engine->db_query2("UPDATE rechargepro_account SET transfer_activation = '0' WHERE rechargeproid = ? LIMIT 1",array($user)); 
    }else{
      $engine->db_query2("UPDATE rechargepro_account SET transfer_activation ='1' WHERE rechargeproid = ? LIMIT 1",array($user));   
    }
    
        $engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("adminid"),"TRANSFER STATUS",$_REQUEST['transferid']."_".$user));
     exit;
}
    
    
    
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