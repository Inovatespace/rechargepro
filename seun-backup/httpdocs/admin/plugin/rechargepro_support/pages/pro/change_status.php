<?php
include "../../../../engine.autoloader.php";
$selection = $_REQUEST['selection'];
$id = $_REQUEST['id'];


$exp = explode(",",$selection);

foreach($exp AS $st){
$engine->db_query2("UPDATE contact_tickets SET admin_status = ? WHERE id = ? LIMIT 1", array($id,$st));
}

?>
