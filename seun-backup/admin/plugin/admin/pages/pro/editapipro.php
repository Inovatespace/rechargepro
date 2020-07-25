<?php
include "../../../../engine.autoloader.php";


if (empty($_REQUEST['account']) || empty($_REQUEST['type']) || empty($_REQUEST['domain']) || empty($_REQUEST['url']) || empty($_REQUEST['max']) || empty($_REQUEST['id']))
{
    echo "<div style='color:red;'>All Fields are compulsory</div>";exit;
}


    $id = $_REQUEST['id'];
	$account = $_REQUEST['account'];
	$type = $_REQUEST['type'];
	$domain = $_REQUEST['domain'];
	$url = $_REQUEST['url'];
	$max = $_REQUEST['max'];


$engine->db_query("UPDATE api SET type=?, domain=?, returnurl=?, max_request=? WHERE acountid = ? AND id = ? LIMIT 1",array($type,$domain,$url,$max,$account,$id));


echo "ok";
exit;

?>