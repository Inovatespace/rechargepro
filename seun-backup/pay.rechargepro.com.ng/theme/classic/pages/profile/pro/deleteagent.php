<?php
include "../../../../../engine.autoloader.php";

$id = $_REQUEST['id'];
    

$profile_creator = $engine->get_session("quickpayid");



$engine->db_query("UPDATE quickpay_account SET profile_creator ='0', quickpay_cordinator ='0' , profile_agent='0' WHERE quickpayid = ? AND profile_creator = ? LIMIT 1",array($id,$profile_creator));


//remove all per
$engine->db_query("DELETE FROM quickpay_services_agent WHERE quickpayid = ?",array($profile_creator));
$engine->db_query("DELETE FROM quickpay_services_fixed WHERE quickpayid = ?",array($profile_creator));
?>