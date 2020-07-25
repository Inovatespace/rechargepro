<?php

class validateResponse extends Api
{

    //KEDCO
    public function __construct($method)
    {
     
    }

    public function verify($parameter)
    {
        
        
$dataPOST = trim(file_get_contents('php://input'));

if (!$dataPOST) {return "";}

$xmlData = simplexml_load_string($dataPOST);
  

$referenceID = trim($xmlData->referenceID);
$amount = trim($xmlData->amount);
$hash = trim($xmlData->hash);

$message = "Successful";
$statuscode = "00";
$hashkey = "A34532DGWER6567RTYH";

$row = self::db_query("SELECT rechargeproid, mobile, name FROM rechargepro_account WHERE mobile = ? LIMIT 1",array($referenceID));
$rechargeproid = $row[0]['rechargeproid'];
$mobile = $row[0]['mobile'];
$CustomerName = $row[0]['name'];


$pass = $referenceID.$amount;
$myhash = hash_hmac('sha512',$pass,$hashkey);





if($hash != $myhash){
$statuscode = "01";
$message = "Invalid Hash";
$rechargeproid = "A0";
}


if(empty($rechargeproid)){
$statuscode = "01";
$message = "Invalid Account";
}


$pass = $referenceID.$amount.$mobile.$CustomerName.$statuscode.$message;
$senthash = hash_hmac('sha512',$pass,$hashkey);



return array("referenceID"=>"$referenceID", "CustomerName"=>"$CustomerName", "otherDetails"=>array("phoneno"=>"$mobile","amount"=>$amount),"currency"=>566,"statusCode"=>"$statuscode","statusMessage"=>"$message","hash"=>$senthash);


    }





}
?>