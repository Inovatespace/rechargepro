<?php
include "../../../../engine.autoloader.php";




if (empty($_REQUEST['account']) || empty($_REQUEST['type']) || empty($_REQUEST['domain']) || empty($_REQUEST['url']) || empty($_REQUEST['max']))
{
    echo "<div style='color:red;'>All Fields are compulsory</div>";exit;
}



function generateRandomString($length = 22) {
    $characters = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

	$account = $_REQUEST['account'];
	$type = $_REQUEST['type'];
	$domain = $_REQUEST['domain'];
	$url = $_REQUEST['url'];
	$max = $_REQUEST['max'];
    $key = generateRandomString();


$engine->db_query("INSERT INTO api (acountid,apikey,type,domain,returnurl,max_request) VALUES(?,?,?,?,?,?) ",array($account,$key,$type,$domain,$url,$max));





echo "ok";
exit;

?>