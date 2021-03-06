<?php
require "../engine.autoloader.php";


if(isset($_REQUEST['flw_ref']) && isset($_REQUEST['id'])){
    
$quickpayref = $_REQUEST['flw_ref'];
$tid = htmlentities($_REQUEST['id']);

$payload = array('flw_ref' => $quickpayref,
  'SECKEY' => $engine->config("rave_secrete_key"), //secret key from pay button generated on rave dashboard
  'normalize' => '1'
);


$data_string = json_encode($payload);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.ravepay.co/flwv3-pug/getpaidx/api/verify');
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

//print_r($response); exit;
  
  //check the status is success
  if ($response['data']['status'] === "successful") {

$quickpayresponse = $response['data']['status'];


 

$row = $engine->db_query("SELECT amount, quickpay_subservice, quickpay_status_code FROM quickpay_transaction_log WHERE transactionid = ? LIMIT 1",array($tid));
$amount_to_charge = $row[0]['amount'];
$quickpay_subservice = $row[0]['quickpay_subservice'];
$quickpay_status_code = $row[0]['quickpay_status_code'];

if($quickpay_status_code == 1){
    //thank you print
    echo "<meta http-equiv='refresh' content='0;url=/thankyou&id=$tid'>"; exit;
}


if(empty($quickpay_subservice)){
      echo "<meta http-equiv='refresh' content='0;url=/thankyou&tid=$quickpayref&quickpay=$tid&er=Invalid Service, Contact support with Transaction ID $tid'>";
   exit; 
}
    
    
      //confirm that the amount is the amount you wanted to charge
if ($response['data']['amount'] >= $amount_to_charge) {

        
if($response['data']['flwMeta']['chargeResponse'] == "00") {
          
          
      

$row = $engine->db_query("SELECT services_category,cordinator_percentage,percentage,bill_formular,bill_quickpayfull_percentage FROM quickpay_services WHERE services_key = ? LIMIT 1",array($quickpay_subservice));
$services_category = $row[0]['services_category'];
$cordinator_percentage = $row[0]['cordinator_percentage'];
$percentage = $row[0]['percentage'];
$bill_formular = $row[0]['bill_formular'];
$bill_quickpayfull_percentage = $row[0]['bill_quickpayfull_percentage'];


$link = "";
switch ($services_category){
	case 1:
    $link = "v1/electricity/auth_transaction.json";
	break;

	case 2:
    case 3:
    case 4:
    $link = "v1/airtime_data/auth_transaction.json";
	break;

	case 5:
    $link = "v1/tv/auth_transaction.json";
	break;
    
    case 6:
    $link = "v1/lottery/auth_transaction.json";
	break;
    
        
    case 7:
    $link = "v1/bills/auth_transaction.json";
	break;
    
   default :   echo "<meta http-equiv='refresh' content='0;url=/thankyou&tid=$quickpayref&quickpay=$tid&er=Invalid Selection, Contact the admin with Transaction ID $tid'>"; exit; break;
}



if($engine->get_session("quickpayid")){
$row = $engine->db_query("SELECT profile_creator FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($engine->get_session("quickpayid"))); 
$profile_creator = $row[0]['profile_creator'];

$engine->db_query("UPDATE quickpay_transaction_log SET quickpay_status = ?,agent_id=?,quickpayid=?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",array("PAID",$profile_creator,$engine->get_session("quickpayid"),$quickpayref,$quickpayresponse,1,$tid));
}else{
$engine->db_query("UPDATE quickpay_transaction_log SET quickpay_status = ?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",array("PAID",$quickpayref,$quickpayresponse,1,$tid));
}


                    //////////////////////////////////////////
                    
                                $percent = ($percentage + $cordinator_percentage + $bill_quickpayfull_percentage) - (($amount_to_charge *
                                    1.5) / 100);
                                if ($bill_formular == 0) {
                                    $percent = (($amount_to_charge * ($percentage + $cordinator_percentage + $bill_quickpayfull_percentage)) /
                                        100) - (($amount_to_charge * 1.5) / 100);
                                }
                    
                                $bp = $percent;
                                if (!in_array($services_category, array(
                                    2,
                                    3,
                                    4))) {
                                    $bp = $percent + 100;
                                }
                    
                    
                                self::db_query("UPDATE quickpay_transaction_log SET refererprofit =?, agentprofit =?, cordprofit =?, quickpayprofit = ? WHERE transactionid = ? LIMIT 1",
                                    array(
                                    0,
                                    0,
                                    0,
                                    $bp,
                                    $tid));
                    //////////////////////////////////////////////////////////


$payload = array("cart"=>$tid);
$responseData = $engine->file_get($payload, $engine->config("website_root")."api/".$link);
          

          
        }else{
            echo "<meta http-equiv='refresh' content='0;url=/thankyou&tid=$quickpayref&quickpay=$tid&er=".$response['data']['flwMeta']['chargeResponse']."'>"; exit;
        }
      }else{
echo "<meta http-equiv='refresh' content='0;url=/thankyou&tid=$quickpayref&quickpay=$tid&er=Wrong Amount Paid, Please Contact support with TID $tid  '>"; exit;
      }
  }else{
    echo "<meta http-equiv='refresh' content='0;url=/thankyou&tid=$quickpayref&quickpay=$tid&er=Unknown Error Occured, Please Contact support with TID $tid'>"; exit;
  }
  
  //success
    //sprint_r($response);
    echo "<meta http-equiv='refresh' content='0;url=/thankyou&id=$tid'>"; exit;
}else{
    echo "<meta http-equiv='refresh' content='0;url=/thankyou&tid=Unknown&quickpay=Unknown&er=Unknown Error Occured, Invalid Transaction'>"; exit;
}



?>