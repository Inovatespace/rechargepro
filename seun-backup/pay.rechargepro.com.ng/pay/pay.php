<?php
include "../engine.autoloader.php";

//$tosend = "";
//foreach($_REQUEST AS $key => $val){
  // $tosend .= $key."=".$val."@";
//}
//$engine->db_query("INSERT INTO bank_ref (response) VALUES (?)", array($tosend));   

//https://www.nextcashandcarry.com.ng/paymentstatus.php?tx=YMA170420060404DKU_6A1

$orderid = "";
$transactionreference = "";
$message = "";

if(isset($_REQUEST['OrderID'])){
  $orderid = $_REQUEST['OrderID'];  
}

if(isset($_REQUEST['TransactionReference'])){
  $transactionreference = $_REQUEST['TransactionReference'];  
}


if(isset($_REQUEST['ErrorMessage'])){
 $message = $_REQUEST['ErrorMessage'];   
}

//https://www.nextcashandcarry.com.ng/transactionstatus/pay.php?TransactionReference=&OrderID=&ErrorMessage=DuplicateOrderID

header("Location: ../paymentstatus&orderid=$orderid&transactionreference=$transactionreference&errormessage=$message");
//<meta http-equiv="Location" content="http://example.com/">
//https://www.nextcashandcarry.com.ng/transactionstatus/pay.php

?>