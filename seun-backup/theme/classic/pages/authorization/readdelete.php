<?php 
include "../../../../engine.autoloader.php";
$id = $_REQUEST['id'];

$rechargeproemail = $engine->get_session("rechargeproid"); 


$engine->db_query("DELETE FROM rechargepro_account_read WHERE readid = ? AND rechargeproid = ? LIMIT 1",array($id,$rechargeproemail)); 


$details = $rechargeproemail."_DELETE READ";
$engine->db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("rechargeproid"),"PDELETE READ",$details));
exit;




?>
