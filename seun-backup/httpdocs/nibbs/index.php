<?php
 header("Generator: rechargepro API");
 header("Content-Type: application/json; charset=UTF-8");
 include "../engine.autoloader.php";
 
if(isset($_REQUEST['u'])){
       
        
        $request  = str_replace("/nibbs/payment/", "",$_REQUEST['u']);
 
  #split the path by '/'
  $params     = explode("/", $request);

  
  if($params[0] == "notification"){  
    
    
   $data_back = json_decode(file_get_contents('php://input'));
   $parameter = array();
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
       
   
   
   $amount = $parameter['amount'];
   $orderId = $parameter['orderId'];
   $msisdn = $parameter['msisdn'];
   $ref = $params[1];
    
    $row = $engine->db_query("SELECT amount,transactionid FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($orderId)); 
if(empty($row[0]['transactionid'])){
          header('HTTP/1.0 422" "Unsuccessful');
      echo '{
"status" : "422",
"message" : "Invalid OrderID"
}';  exit;   
}else{
    
    if($row['amount'] > $amount){
               header('HTTP/1.0 422" "Unsuccessful');
      echo '{
"status" : "422",
"message" : "Invalid Amount"
}';  exit;   
        
    }else{
        //process payment
        //store ref
    header('HTTP/1.0 200" "Successful'); 
   echo '{
"status" : "200"
}';  exit;  

  }
}

}else{  
    
    $row = $engine->db_query("SELECT transactionid FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($params[0])); 
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

}
  
  }else{
        header('HTTP/1.0 422" "Unsuccessful');
      echo '{
"status" : "422",
"message" : "Invalid OrderID"
}';  exit; 
    }
    
    
    
    
?>