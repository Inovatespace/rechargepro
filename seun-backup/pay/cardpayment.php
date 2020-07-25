<?php
require "../engine.autoloader.php";


if(isset($_REQUEST['flw_ref']) && isset($_REQUEST['id'])){
    
$rechargeproref = $_REQUEST['flw_ref'];
$tid = htmlentities($_REQUEST['id']);

$payload = array('flwref' => $rechargeproref,
  'SECKEY' => $engine->config("rave_secrete_key"), //secret key from pay button generated on rave dashboard
  'normalize' => '1'
);

//$payload = array(
        //    "SECKEY" => $engine->config("rave_secrete_key"),
        //    "flw_ref" => $rechargeproref
       // );
//https://rechargepro.com.ng/pay/cardpayment.php?flw_ref=FLW-MOCK-7f1f2dd615a5991ab29b23c3b8c847fa&id=59081
//https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify
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
 // print_r($response);
 // echo "ddd"; exit;
            //
//print_r($response); exit;
  
  //check the status is success
if (!isset($response["status"])) {
            echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Network Error, Contact support with Transaction ID $tid'>";
   exit; 
        }

        if (!isset($response["data"]["chargecode"])) {
            
            echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Service Error, Contact support with Transaction ID $tid'>";
        }
        
       

        if ($response["data"]["chargecode"] == "00") {
           

 

$row = $engine->db_query("SELECT rechargeproid, amount, rechargepro_subservice, rechargepro_status_code FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($tid));
$brechargeproid = $row[0]['rechargeproid'];
$amount_to_charge = $row[0]['amount'];
$rechargepro_subservice = $row[0]['rechargepro_subservice'];
$rechargepro_status_code = $row[0]['rechargepro_status_code'];

if($rechargepro_status_code == 1){
    //thank you print
    echo "<meta http-equiv='refresh' content='0;url=/invoice&id=".$brechargeproid."_".$tid."'>"; exit;
}


if(empty($rechargepro_subservice)){
      echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Invalid Service, Contact support with Transaction ID $tid'>";
   exit; 
}
    
    
      //confirm that the amount is the amount you wanted to charge


        
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
           
          
            
        if(isset($response["data"]["chargedamount"])){$response["data"]["charged_amount"] = $response["data"]["chargedamount"];}
        
        if(isset($response["data"]["card"]["card_tokens"]["embedtoken"])){$response["data"]["card"]["card_tokens"]["0"]["embedtoken"] = $response["data"]["card"]["card_tokens"]["embedtoken"];}
        
        
        
        
                
$amountcharged =  $response["data"]["charged_amount"];   

$row = $engine->db_query("SELECT services_category,cordinator_percentage,percentage,bill_formular,bill_rechargeprofull_percentage FROM rechargepro_services WHERE services_key = ? LIMIT 1",array($rechargepro_subservice));
$services_category = $row[0]['services_category'];
$cordinator_percentage = $row[0]['cordinator_percentage'];
$percentage = $row[0]['percentage'];
$bill_formular = $row[0]['bill_formular'];
$bill_rechargeprofull_percentage = $row[0]['bill_rechargeprofull_percentage'];


$totalpercentage = $percentage+$bill_rechargeprofull_percentage+$cordinator_percentage;
if($bill_formular == 0){
$tfee = ($amount_to_charge * $totalpercentage) / 100;
}else{
$tfee = $totalpercentage;  
}


$amountremaining = $amountcharged - $amount_to_charge;

if(($amount_to_charge-$tfee) < $amountremaining){
    $amount_to_charge = $amountremaining;
}



$thepercentage = ceil(($amountcharged * 1.5) / 100);
if($response["data"]["appfee"] > $thepercentage){
$thepercentage = ceil(($amountcharged * 3.5) / 100);

//LOCAL CARD
echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Only Local card allowed, Contact support with Transaction ID $tid'>";
exit; 
}



$row = $engine->db_query("SELECT rechargeprorole FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($brechargeproid));
$rechargeprorole = $row[0]['rechargeprorole'];




$link = "";
switch ($services_category){
	case 1:
    $link = "pro/electricity/complete_transaction.json";
	break;

	case 2:
    case 3:
    case 4:
    $link = "pro/airtime_data/complete_transaction.json";
	break;

	case 5:
    $link = "pro/tv/complete_transaction.json";
	break;
    
    case 6:
    $link = "pro/lottery/complete_transaction.json";
	break;
    
        
    case 7:
    $link = "pro/bills/complete_transaction.json";
	break;
    
   default :   echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=Invalid Selection, Contact the admin with Transaction ID $tid'>"; exit; break;
}



if($engine->get_session("rechargeproid")){
$row = $engine->db_query("SELECT profile_creator FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($engine->get_session("rechargeproid"))); 
$profile_creator = $row[0]['profile_creator'];

$engine->db_query("UPDATE rechargepro_transaction_log SET amount =?, rechargepro_status = ?,agent_id=?,rechargeproid=?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",array($amount_to_charge,"PAID",$profile_creator,$engine->get_session("rechargeproid"),$rechargeproref,"Completed",1,$tid));
}else{
$engine->db_query("UPDATE rechargepro_transaction_log SET amount= ?,  rechargepro_status = ?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",array($amount_to_charge,"PAID",$rechargeproref,"Completed",1,$tid));
}


                    //////////////////////////////////////////
                    
                    
                       $bp = 0;
                                $engine->db_query("UPDATE rechargepro_transaction_log SET refererprofit =?, agentprofit =?, cordprofit =?, rechargeproprofit = ? WHERE transactionid = ? LIMIT 1",
                                    array(
                                    0,
                                    0,
                                    0,
                                    $bp,
                                    $tid));                 


//$payload = array("cart"=>$tid);
$payload = array("cart"=>$tid,"tid"=>$tid,"serial"=>"web","private_key"=>"web");
$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/".$link);

          
 echo "<meta http-equiv='refresh' content='0;url=/invoice&id=".$brechargeproid."_".$tid."'>"; exit;
          
        }else{
            echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=$rechargeproref&rechargepro=$tid&er=".$response['data']['chargeResponse']."'>"; exit;
        }
      
   
}else{
    echo "<meta http-equiv='refresh' content='0;url=/invoice&tid=Unknown&rechargepro=Unknown&er=Unknown Error Occured, Invalid Transaction'>"; exit;
}

//https://localhost/rechargepro/pay/cardpayment.php?flw_ref=FLW046214049&id=1

?>