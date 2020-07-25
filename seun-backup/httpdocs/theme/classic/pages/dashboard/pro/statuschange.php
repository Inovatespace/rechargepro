<?php
include "../../../../../engine.autoloader.php";

if(isset($_REQUEST['trecharge4id'])){
    
$recharge4role = $engine->get_session("recharge4role"); 
$profile_creator = $engine->get_session("recharge4id");
    
    $status = $_REQUEST['status'];
    $user = $_REQUEST['trecharge4id'];
    
    $sta = "0";
    if($status == "2"){
    $sta = "1";
    }
    
    
    //switch ($_REQUEST['what']){ 
	//case "ac":
    switch ($recharge4role){
	case "1":
     $engine->db_query("UPDATE recharge4_account SET active =? WHERE recharge4id = ? LIMIT 1",array($sta,$user));
	break;

	case "2":
    $engine->db_query("UPDATE recharge4_account SET active =? WHERE recharge4id = ? AND profile_agent = ? LIMIT 1",array($sta,$user,$profile_creator));
	break;
    
    case "3":
    $engine->db_query("UPDATE recharge4_account SET active =? WHERE recharge4id = ? AND profile_creator = ? LIMIT 1",array($sta,$user,$profile_creator));
	break;
};
   
	//break;

	//case "live":
    //$engine->db_query("UPDATE recharge4_account SET profile_live_account =? WHERE recharge4id = ? AND profile_creator = ? LIMIT 1",array($sta,$user,$profile_creator));
	//break;

    //}
    
     
}
?>