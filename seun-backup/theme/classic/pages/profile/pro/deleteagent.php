<?php
include "../../../../../engine.autoloader.php";

$id = $_REQUEST['id'];
    
//if($id == "172"){exit;}
$profile_creator = $engine->get_session("rechargeproid");



$engine->db_query("UPDATE rechargepro_account SET profile_creator ='0', rechargepro_cordinator ='0' , profile_agent='0' WHERE rechargeproid = ? AND profile_creator = ? LIMIT 1",array($id,$profile_creator));


//remove all per
$engine->db_query("DELETE FROM rechargepro_services_agent WHERE rechargeproid = ?",array($profile_creator));
$engine->db_query("DELETE FROM rechargepro_services_fixed WHERE rechargeproid = ?",array($profile_creator));
?>