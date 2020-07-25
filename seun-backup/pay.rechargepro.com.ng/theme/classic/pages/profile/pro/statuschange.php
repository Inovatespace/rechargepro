<?php
include "../../../../../engine.autoloader.php";

if(isset($_REQUEST['tquickpayid'])){
    
$quickpayrole = $engine->get_session("quickpayrole"); 
$profile_creator = $engine->get_session("quickpayid");
    
    $status = $_REQUEST['status'];
    $user = $_REQUEST['tquickpayid'];
    
    $sta = "0";
    if($status == "2"){
    $sta = "1";
    }
    
    
    //switch ($_REQUEST['what']){ 
	//case "ac":
    switch ($quickpayrole){
	case "1":
     $engine->db_query("UPDATE quickpay_account SET active =? WHERE quickpayid = ? LIMIT 1",array($sta,$user));
	break;

	case "2":
    $engine->db_query("UPDATE quickpay_account SET active =? WHERE quickpayid = ? AND profile_agent = ? LIMIT 1",array($sta,$user,$profile_creator));
	break;
    
    case "3":
    $engine->db_query("UPDATE quickpay_account SET active =? WHERE quickpayid = ? AND profile_creator = ? LIMIT 1",array($sta,$user,$profile_creator));
	break;
};
   
	//break;

	//case "live":
    //$engine->db_query("UPDATE quickpay_account SET profile_live_account =? WHERE quickpayid = ? AND profile_creator = ? LIMIT 1",array($sta,$user,$profile_creator));
	//break;

    //}
    
     
}
?>