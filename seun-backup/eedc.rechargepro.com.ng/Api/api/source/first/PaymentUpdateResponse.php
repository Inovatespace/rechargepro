<?php

class PaymentUpdateResponse extends Api
{

    //KEDCO
    public function __construct($method)
    {
     
    }


    public function update($parameter)
    {

       
        
$dataPOST = trim(file_get_contents('php://input'));

if (!$dataPOST) {return "";}

$xmlData = simplexml_load_string($dataPOST);


  

$referenceID = trim($xmlData->referenceID);
$transReference = trim($xmlData->transReference);
$totalamount = trim($xmlData->totalamount);

$hash = trim($xmlData->hash);

$hashkey = "A34532DGWER6567RTYH";
$senthash = "";
$PaymentReference = "0";
$message = "Successful";
$statuscode = "00";


    


$pass = $referenceID.$totalamount.$transReference;
$myhash = hash_hmac('sha512',$pass,$hashkey);



$pass = $referenceID.$totalamount.$transReference.$PaymentReference.$statuscode.$message;
$senthash = hash_hmac('sha512',$pass,$hashkey);



$row = self::db_query("SELECT transactionid FROM rechargepro_transaction_log WHERE transaction_reference = ? LIMIT 1",array("FIRST".$transReference));
if(!empty($row[0]['transactionid'])){
    
return array("referenceID"=>$referenceID, "transReference"=>$transReference, "PaymentReference"=>$PaymentReference, "ResponseCode"=>$statuscode, "ResponseDesc"=>$message,"hash"=>$senthash);
    exit;
}

if($hash != $myhash){
 $statuscode = "01";
$pass = $referenceID.$totalamount.$transReference.$PaymentReference.$statuscode.$message;
$senthash = hash_hmac('sha512',$pass,$hashkey); 
  
return array("referenceID"=>$referenceID, "transReference"=>$transReference, "PaymentReference"=>$PaymentReference, "ResponseCode"=>$statuscode, "ResponseDesc"=>"Invalid Hash","hash"=>$senthash);
    exit;
}



        $row = self::db_query("SELECT rechargeproid, mobile, name, ac_ballance,profile_creator FROM rechargepro_account WHERE mobile = ? LIMIT 1",array($referenceID));
        $rechargeproid = $row[0]['rechargeproid'];
        $mobile = $row[0]['mobile'];
        $CustomerName = $row[0]['name'];
        $oldac_ballance = $row[0]['ac_ballance'];
        $profile_creator = $row[0]['profile_creator'];
        
        if(empty($rechargeproid)){
        $statuscode = "01";
        $message = "Invalid Account";
        }else{
     $payment = $totalamount;
     $myip = self::getRealIpAddr();
        
     $ac_ballance = $oldac_ballance + $payment;
     self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($ac_ballance,$rechargeproid));  
     
     $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (rechargepro_status,account_meter,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,ip,amount,rechargepro_print) VALUES (?,?,?,?,?,?,?,?,?,?,?)",array("PAID",$rechargeproid,$profile_creator,$rechargeproid,"FIRST".$transReference,"Credit","FIRST-BILLS","1",$myip,$payment,'{"details":"DONE","old_bal":"'.$oldac_ballance.'"}')); 
     
     $PaymentReference = $insertid."_".date("ymd");
    }
    

$pass = $referenceID.$totalamount.$transReference.$PaymentReference.$statuscode.$message;
$senthash = hash_hmac('sha512',$pass,$hashkey);


return array("referenceID"=>$referenceID, "transReference"=>$transReference, "PaymentReference"=>$PaymentReference, "ResponseCode"=>$statuscode, "ResponseDesc"=>$message,"hash"=>$senthash);


    }



}
?>