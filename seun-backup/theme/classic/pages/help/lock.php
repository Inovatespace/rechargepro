<?php
include "../../../../engine.autoloader.php";


if(!empty($_REQUEST['id'])){

$cdate = date("Y-m-d H:i:s");
$engine->db_query("UPDATE contact_tickets SET lastupdate = ?, status = ?, locked = ? WHERE id = ? LIMIT 1",array($cdate,1,1,$_REQUEST['id']));
}
?>

