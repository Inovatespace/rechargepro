<?php
require "../engine.autoloader.php";


if(isset($_REQUEST['flw_ref']) && isset($_REQUEST['id'])){
    
$rechargeproref = $_REQUEST['flw_ref'];
$tid = htmlentities($_REQUEST['id']);

$payload = array('flwref' => $rechargeproref,
  'SECKEY' => $engine->config("rave_secrete_key"), //secret key from pay button generated on rave dashboard
  'normalize' => '1'
);

//https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/verify
$data_string = json_encode($payload);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
//curl_setopt($ch, CURLOPT_URL, 'https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/verify');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 

curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($data_string)));
    
$result = curl_exec($ch);
$response = json_decode($result, true);
//FLW-MOCK-644652585dddde3e31c1035df6eab9c3



//https://rechargepro.com.ng/pay/topuppayment.php?flw_ref=FLW-MOCK-c2638f470d8b69965ea3226ce895650a&id=59084


if (!isset($response["status"])) {
            echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Network Error, Contact support with Transaction ID $tid u'>"; exit;
        }

        if (!isset($response["data"]["chargecode"])) {
           echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Network Error1, Contact support with Transaction ID $tid v'>"; exit;
        }

        if ($response["data"]["chargecode"] == "00") {
            
            

$row = $engine->db_query("SELECT rechargeproid, amount, rechargepro_subservice, rechargepro_status_code FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($tid));
$amount_to_charge = $row[0]['amount'];
$rechargepro_subservice = $row[0]['rechargepro_subservice'];
$rechargepro_status_code = $row[0]['rechargepro_status_code'];
$rechargeproid = $row[0]['rechargeproid'];

if($rechargepro_status_code == 1){
    //thank you print
    echo "<meta http-equiv='refresh' content='0;url=/invoice&id=".$rechargeproid."_".$tid."'>"; exit;
}


if(empty($rechargepro_subservice)){
      echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Invalid Service, Contact support with Transaction ID $tid w'>";
   exit; 
}


        
          if(isset($response["data"]["tx"])){
              $response["data"]["charged_amount"]  = $response["data"]["tx"]["charged_amount"];
              $response["data"]["flwRef"] = $response["data"]["tx"]["flwRef"];
            }
            

          
          if(isset($response["data"]["flwref"])){
            $response["data"]["flwRef"] = $response["data"]["flwref"];
            }
            
            if(isset($response["data"]["flwref"])){
            $response["data"]["flwRef"] = $response["data"]["flwref"];
            }
    

           //["embedtoken"]
           
          if(isset($response["data"]["flwRef"])){
            
        if(isset($response["data"]["chargedamount"])){$response["data"]["charged_amount"] = $response["data"]["chargedamount"];}
        
       // if(isset($response["data"]["card"]["card_tokens"]["embedtoken"])){$response["data"]["card"]["card_tokens"]["0"]["embedtoken"] = $response["data"]["card"]["card_tokens"]["embedtoken"];}
        
        
        
              $thepercentage = ceil(($response["data"]["charged_amount"] * 1.5) / 100);
              if($response["data"]["appfee"] > $thepercentage){
                $thepercentage = ceil(($response["data"]["charged_amount"] * 3.5) / 100);
                
                 //LOCAL CARD
                      echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Only Local card allowed, Contact support with Transaction ID $tid x'>";
   exit; 
              }
               
                    
$amount_to_charge = $response["data"]["charged_amount"]-$thepercentage;        



if($engine->get_session("rechargeproid")){
$row = $engine->db_query("SELECT profile_creator FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($engine->get_session("rechargeproid"))); 
$profile_creator = $row[0]['profile_creator'];

$engine->db_query("UPDATE rechargepro_transaction_log SET amount= ?,  rechargepro_status = ?,agent_id=?,rechargeproid=?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",array($amount_to_charge,"PAID",$profile_creator,$engine->get_session("rechargeproid"),$rechargeproref,$response['message'],1,$tid));
}else{
$engine->db_query("UPDATE rechargepro_transaction_log SET amount= ?,  rechargepro_status = ?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",array($amount_to_charge,"PAID",$rechargeproref,$response['message'],1,$tid));
}





if($rechargepro_subservice == "Transfer" || $rechargepro_subservice == "TRANSFER"){
$payload = array("tid"=>$tid,"serial"=>"web","private_key"=>"web");
$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/pro/transfer/complete_transaction.json");
          }
          
          
if($rechargepro_subservice == "Topup" || $rechargepro_subservice == "TOPUP"){
$payload = array("tid"=>$tid,"serial"=>"web","private_key"=>"web");
$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/pro/transfer/complete_transaction.json");
          }
   
         
            
             echo "<meta http-equiv='refresh' content='0;url=/invoice&id=".$rechargeproid."_".$tid."'>"; exit;
            
            
            
            
            
            
            
            
        } else {
            
             echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=".$response['data']["chargemessage"]." y'>"; exit;
             
        
        }



  }else{
    echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Unknown Error Occured, Please Contact support with TID $tid z'>"; exit;
  }
  
  //success
    //sprint_r($response);
   
}else{
    echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=Unknown&rechargepro=Unknown&er=Unknown Error Occured, Invalid Transaction 0'>"; exit;
}



?>