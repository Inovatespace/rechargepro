<?php 
include "../../../../engine.autoloader.php";
$id = $_REQUEST['id'];

$rechargeproemail = $engine->get_session("rechargeproemail"); 


$engine->db_query("DELETE FROM rechargepro_access WHERE id = ? AND email = ? LIMIT 1",array($id,$rechargeproemail)); 


$details = $rechargeproemail."_DELETE ACCESS";
$engine->db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("rechargeproid"),"PDELETE ACCESS",$details));
exit;




?>
