<?php
include "../../../../engine.autoloader.php";




if(isset($_REQUEST['buserid'])){
    
    if($engine->get_session("adminid")){
    $user = $_REQUEST['buserid'];
    

$row = $engine->db_query2("SELECT rechargeproid, name, email, mobile,rechargeprorole FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($user));

if(!empty($row[0]['rechargeproid'])){

    
$engine->put_session("rechargeproid",$row[0]['rechargeproid']);
$engine->put_session("rechargeprorole",$row[0]['rechargeprorole']);
$engine->put_session("name",$row[0]['name']);
$engine->put_session("rechargeproemail",$row[0]['email']);

}

}
  exit;    
}
?>