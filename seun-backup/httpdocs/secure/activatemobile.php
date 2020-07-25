<?php
include ('../engine.autoloader.php');
//require ("../engine/phprotector/PhProtector.php");
require '../engine/PHPMailer/PHPMailerAutoload.php';



/* TESTING environment (show all PHP errors!) */
//$prot = new PhProtector("../log/log.xml", true);
//if ($prot->isMalicious()){
//    header("location: ../engine/errorpages/index.html"); //if an atack is found, it will be redirected to this page :)
//    die();
//}


   function curlit($from,$message){
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, "http://api.smartsmssolutions.com/smsapi.php");
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS, "username=spl&password=change&sender=NEXTCC&recipient=$from&message=$message");
       // Set a referer
       curl_setopt($ch, CURLOPT_REFERER, "http://nextcashandcarry.com.ng");
       // User agent
       curl_setopt($ch, CURLOPT_USERAGENT, "Firefox (WindowsXP) – Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
       // Include header in result? (0 = yes, 1 = no)
       curl_setopt($ch, CURLOPT_HEADER, 0);
       // Should cURL return or print out the data? (true = return, false = print)
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       // Timeout in seconds
       curl_setopt($ch, CURLOPT_TIMEOUT, 10);
       // Download the given URL, and return output
       $output = curl_exec($ch);
       // Close the cURL resource, and free system resources
       curl_close($ch); 
       
       return $output;
    }
    
    
    
      if(!isset($_REQUEST['type'])){
        echo "An Error Occured"; exit;
        }
        
        
        
    
  if($_REQUEST['type'] == 1){
    $username = $_REQUEST['username'];
    
    $rowa = $engine->db_query("SELECT mobile FROM members WHERE mobile = ? LIMIT 1",array($username));
    $mainid = $rowa[0]['mobile'];
    
    if(!empty($mainid)){
     echo "Phone Number is in use"; exit;     
    }
    
    $engine->db_query("UPDATE members SET mobile = ? WHERE memberid = ? LIMIT 1",array($username,$engine->get_session("userid")));
  
    
    echo "ok";
    exit;
  }  
    
    
    
    
    
    
    
    
     if($_REQUEST['type'] == 2){ 
    
    $rowa = $engine->db_query("SELECT mobile FROM members WHERE memberid = ? LIMIT 1",array($engine->get_session("userid")));
    $mainid = $rowa[0]['mobile'];
    if(empty($mainid)){
     echo "Phone Number Error"; exit;     
    }
    
    $code = $engine->RandomString("4",5);
    
    $engine->db_query("INSERT INTO temp_code (mobile,code) VALUES (?,?)",array($mainid,$code));
    
    $message = "Next Account activation code : ".$code;
    curlit($mainid,$message);
    
    echo "ok";
    exit;
    }
    



 if($_REQUEST['type'] == 3){

$username = $_REQUEST['username'];

    $date = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
    $rowa = $engine->db_query("SELECT mobile,code FROM temp_code WHERE code = ? AND date >= ? LIMIT 1",array($username,$date));
    $mainid = $rowa[0]['mobile'];
    if(empty($mainid)){
     echo "The code entered has been used"; exit;     
    }
    
    
    $engine->db_query("UPDATE temp_code SET status = '1' WHERE code = ? LIMIT 1",array($username));
    $engine->db_query("UPDATE members SET activemobile = ? WHERE mobile = ? LIMIT 1",array($username,$mainid));
    
    

echo "ok"; exit;
}

?>