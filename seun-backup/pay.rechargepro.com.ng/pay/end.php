<?php
include "../engine.autoloader.php";

//$tosend = "";
//foreach($_REQUEST AS $key => $val){
 //$tosend .= $key."=".$val."@";
//}
//$engine->db_query("INSERT INTO bank_ref (response) VALUES (?)", array($tosend));   




$bank_ref = $_REQUEST['id'];
$next_ref = $_REQUEST['txRef'];
$amount = $_REQUEST['amount'];
$response = $_REQUEST['status'];
$ip = $_REQUEST['IP'];
$charged_amount = $_REQUEST['charged_amount'];
$createdAt = $_REQUEST['createdAt'];

$engine->db_query("UPDATE quickpay_transaction_log	SET bank_response=? WHERE next_ref = ?", array($response,$next_ref));



//id=30078@
//txRef=YMA170420060404DKU_6A1@
//createdAt=2017-04-20T17:05:11.000Z@
//amount=400@
//charged_amount=406.4@
//status=successful@
//IP=169.159.98.74@
//currency=NGN@
//customer=Array@
//entity=Array@
?>