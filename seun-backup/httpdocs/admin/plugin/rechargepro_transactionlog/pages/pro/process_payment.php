<?php include "../../../../../engine.autoloader.php";

$display = "";
if(isset($_REQUEST['flw_ref']) && isset($_REQUEST['id'])){
    
$rechargeproref = $_REQUEST['flw_ref'];
$tid = htmlentities($_REQUEST['id']);

$payload = array('flw_ref' => $rechargeproref,
  'SECKEY' => $engine->config("rave_secrete_key"), //secret key from pay button generated on rave dashboard
  'normalize' => '1'
);
//
$response = $engine->file_get($payload, "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify");


  if ($response['status'] === "success") {

$rechargeproresponse = $response['status'];


 

$row = $engine->db_query("SELECT amount, rechargepro_subservice, rechargepro_status_code FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($tid));
$amount_to_charge = $row[0]['amount'];
$rechargepro_subservice = $row[0]['rechargepro_subservice'];
$rechargepro_status_code = $row[0]['rechargepro_status_code'];

if($rechargepro_status_code == 1){
    //thank you print
   $display = "Transaction Completed"; 
}


if(empty($rechargepro_subservice) && empty($display)){
$display = "Invalid Selection, Contact the admin with Transaction ID $tid";
}
    
    
      //confirm that the amount is the amount you wanted to charge
if ($response['data']['amount'] >= $amount_to_charge && empty($display)) {

        
if($response['data']['chargecode'] == "00" && empty($display)) {
          
          
      

$row = $engine->db_query("SELECT services_category FROM rechargepro_services WHERE services_key = ? LIMIT 1",array($rechargepro_subservice));
$services_category = $row[0]['services_category'];



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
    
   default :   $display =  "Invalid Selection, Contact the admin with Transaction ID $tid'>"; break;
}



if(empty($display)){
$engine->db_query("UPDATE rechargepro_transaction_log SET rechargepro_status = ?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",array("PAID",$rechargeproref,$rechargeproresponse,1,$tid));
}





$payload = array("cart"=>$tid);
$responseData = $engine->file_get($payload, $engine->config("website_root")."api/".$link);
          

          
        }
        
        if($response['data']['chargecode'] != "00" && empty($display)) {
$display =  "".$response['data']['chargecode']."'>";
        }
        
        
      }
      
      if ($response['data']['amount'] != $amount_to_charge && empty($display)) {
$display = "Wrong Amount Paid, Please Contact support with TID $tid  '>";
      }
      
  }else{
$display =  "Unknown Error Occured, Please Contact support with TID $tid'>";
  }
  
if(empty($display)){
    $display =  ""; }
    
}else{
   $display =  "Unknown&er=Unknown Error Occured, Invalid Transaction'>";
}

//<div class='NnInformation'>	
?>