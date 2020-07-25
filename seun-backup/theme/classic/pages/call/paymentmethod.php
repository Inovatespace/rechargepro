<?php
require "../../../../engine.autoloader.php";



if(isset($_REQUEST['paymentmethod'])){
    $paymentmethod = $_REQUEST['paymentmethod'];
$engine->db_query("UPDATE rechargepro_transaction_log SET payment_method = ? WHERE transactionid = ?",array($paymentmethod,$engine->get_session("cartid")));
            

    echo "ok@".$engine->get_session("cartid"); exit;        
    
    }




?>