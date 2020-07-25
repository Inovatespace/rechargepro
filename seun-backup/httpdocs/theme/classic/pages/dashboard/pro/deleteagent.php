<?php
include "../../../../../engine.autoloader.php";

$id = $_REQUEST['id'];
    
//if($id == "172"){exit;}
$profile_creator = $engine->get_session("recharge4id");



$engine->db_query("UPDATE recharge4_account SET profile_creator ='0', recharge4_cordinator ='0' , profile_agent='0' WHERE recharge4id = ? AND profile_creator = ? LIMIT 1",array($id,$profile_creator));


//remove all per
$engine->db_query("DELETE FROM recharge4_services_agent WHERE recharge4id = ?",array($profile_creator));
$engine->db_query("DELETE FROM recharge4_services_fixed WHERE recharge4id = ?",array($profile_creator));
?>