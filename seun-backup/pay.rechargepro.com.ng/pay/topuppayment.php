<?php
require "../engine.autoloader.php";


if(isset($_REQUEST['flw_ref']) && isset($_REQUEST['id'])){
    
$quickpayref = $_REQUEST['flw_ref'];
$tid = htmlentities($_REQUEST['id']);

$payload = array('flwref' => $quickpayref,
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



//https://quickpay.com.ng/pay/topuppayment.php?flw_ref=FLW-MOCK-c2638f470d8b69965ea3226ce895650a&id=59084


if (!isset($response["status"])) {
            echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$quickpayref&quickpay=$tid&er=Network Error, Contact support with Transaction ID $tid u'>"; exit;
        }

        if (!isset($response["data"]["chargecode"])) {
           echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$quickpayref&quickpay=$tid&er=Network Error1, Contact support with Transaction ID $tid v'>"; exit;
        }

        if ($response["data"]["chargecode"] == "00") {
            
            

$row = $engine->db_query("SELECT quickpayid, amount, quickpay_subservice, quickpay_status_code FROM quickpay_transaction_log WHERE transactionid = ? LIMIT 1",array($tid));
$amount_to_charge = $row[0]['amount'];
$quickpay_subservice = $row[0]['quickpay_subservice'];
$quickpay_status_code = $row[0]['quickpay_status_code'];
$quickpayid = $row[0]['quickpayid'];

if($quickpay_status_code == 1){
    //thank you print
    echo "<meta http-equiv='refresh' content='0;url=/invoice&id=".$quickpayid."_".$tid."'>"; exit;
}


if(empty($quickpay_subservice)){
      echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$quickpayref&quickpay=$tid&er=Invalid Service, Contact support with Transaction ID $tid w'>";
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
                      echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$quickpayref&quickpay=$tid&er=Only Local card allowed, Contact support with Transaction ID $tid x'>";
   exit; 
              }
               
                    
$amount_to_charge = $response["data"]["charged_amount"]-$thepercentage;        



if($engine->get_session("quickpayid")){
$row = $engine->db_query("SELECT profile_creator FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($engine->get_session("quickpayid"))); 
$profile_creator = $row[0]['profile_creator'];

$engine->db_query("UPDATE quickpay_transaction_log SET amount= ?,  quickpay_status = ?,agent_id=?,quickpayid=?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",array($amount_to_charge,"PAID",$profile_creator,$engine->get_session("quickpayid"),$quickpayref,$response['message'],1,$tid));
}else{
$engine->db_query("UPDATE quickpay_transaction_log SET amount= ?,  quickpay_status = ?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",array($amount_to_charge,"PAID",$quickpayref,$response['message'],1,$tid));
}





if($quickpay_subservice == "Transfer" || $quickpay_subservice == "TRANSFER"){
$payload = array("tid"=>$tid,"serial"=>"web","private_key"=>"web");
$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/local/transfer/complete_transaction.json");
          }
          
          
if($quickpay_subservice == "Topup" || $quickpay_subservice == "TOPUP"){
$payload = array("tid"=>$tid,"serial"=>"web","private_key"=>"web");
$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/local/transfer/complete_transaction.json");
          }
   
         
            
             echo "<meta http-equiv='refresh' content='0;url=/invoice&id=".$quickpayid."_".$tid."'>"; exit;
            
            
            
            
            
            
            
            
        } else {
            
             echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$quickpayref&quickpay=$tid&er=".$response['data']["chargemessage"]." y'>"; exit;
             
        
        }



  }else{
    echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$quickpayref&quickpay=$tid&er=Unknown Error Occured, Please Contact support with TID $tid z'>"; exit;
  }
  
  //success
    //sprint_r($response);
   
}else{
    echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=Unknown&quickpay=Unknown&er=Unknown Error Occured, Invalid Transaction 0'>"; exit;
}



?>