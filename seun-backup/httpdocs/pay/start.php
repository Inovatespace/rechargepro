<?php
include "../engine.autoloader.php";
exit;
//$tosend = "";
//foreach($_REQUEST AS $key => $val){
// $tosend .= $key."=".$val."@";
//}
//


// Retrieve the request's body
//$body = @file_get_contents("php://input");

// retrieve the signature sent in the reques header's.
$signature = (isset($_SERVER['Verif-hash']) ? $_SERVER['Verif-hash'] : '');

/* It is a good idea to log all events received. Add code *
 * here to log the signature and body to db or file       */

if (!$signature) {
    // only a post with rave signature header gets our attention
    //exit();
}

// Store the same signature on your server as an env variable and check against what was sent in the headers
$local_signature = "loverechargepro";//getenv('SECRET_HASH');

// confirm the event's signature
if( $signature !== $local_signature ){
  // silently forget this ever happened
  //exit();
}

http_response_code(200); // PHP 5.4 or greater
// parse event (which is json string) as object
// Give value to your customer but don't give any output
// Remember that this is a call from rave's servers and 
// Your customer is not seeing the response here at all
$response = json_encode($_REQUEST);

$engine->db_query("INSERT INTO rechargepro_transaction_log (bank_ref,rechargepro_status) VALUES (?,?)", array($response,"PAID"));  
if ($response->body->status == 'successful') {
    # code...
    // TIP: you may still verify the transaction
    		// before giving value.
}

 

$bank_ref = $_REQUEST['id'];
$next_ref = $_REQUEST['txRef'];
$amount = $_REQUEST['amount'];
$response = $_REQUEST['status'];
$ip = $_REQUEST['IP'];

$engine->db_query("UPDATE rechargepro_transaction_log	SET bank_ref=?, bank_response=? WHERE transactionid = ?", array($bank_ref,$response,$next_ref));



//id=30078@txRef=YMA170420060404DKU_6A1@createdAt=2017-04-20T17:05:11.000Z@amount=400@charged_amount=406.4@status=pending@IP=169.159.98.74@currency=NGN@customer=Array@entity=Array@
?>