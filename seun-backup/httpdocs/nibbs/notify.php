<?php
 header("Generator: QuickPay API");
 header("Content-Type: application/json; charset=UTF-8");
 include "../engine.autoloader.php";
 
if(isset($_REQUEST['u'])){
       
        
        $request  = str_replace("/nibbs/notification/", "", $_REQUEST['u']);
 
  #split the path by '/'
  $params     = explode("/", $request);
  
  
  $row = $engine->db_query("SELECT transactionid FROM quickpay_transaction_log WHERE transactionid = ? LIMIT 1",array($params[0])); 
if(empty($row[0]['transactionid'])){
          header('HTTP/1.0 422" "Unsuccessful');
      echo '{
"status" : "422",
"message" : "Invalid OrderID"
}';  exit;   
}else{
    header('HTTP/1.0 200" "Successful'); 
   echo '{
"status" : "200"
}';  exit;    
}
  
  }else{
        header('HTTP/1.0 422" "Unsuccessful');
      echo '{
"status" : "422",
"message" : "Invalid OrderID"
}';  exit; 
    }
?>