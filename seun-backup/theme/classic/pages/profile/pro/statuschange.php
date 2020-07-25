<?php
include "../../../../../engine.autoloader.php";

if(isset($_REQUEST['trechargeproid'])){
    
$rechargeprorole = $engine->get_session("rechargeprorole"); 
$profile_creator = $engine->get_session("rechargeproid");
    
    $status = $_REQUEST['status'];
    $user = $_REQUEST['trechargeproid'];
    
    $sta = "0";
    if($status == "2"){
    $sta = "1";
    }
    
    
    //switch ($_REQUEST['what']){ 
	//case "ac":
    switch ($rechargeprorole){
	case "1":
     $engine->db_query("UPDATE rechargepro_account SET active =? WHERE rechargeproid = ? LIMIT 1",array($sta,$user));
	break;

	case "2":
    $engine->db_query("UPDATE rechargepro_account SET active =? WHERE rechargeproid = ? AND profile_agent = ? LIMIT 1",array($sta,$user,$profile_creator));
	break;
    
    case "3":
    $engine->db_query("UPDATE rechargepro_account SET active =? WHERE rechargeproid = ? AND profile_creator = ? LIMIT 1",array($sta,$user,$profile_creator));
	break;
};
   
	//break;

	//case "live":
    //$engine->db_query("UPDATE rechargepro_account SET profile_live_account =? WHERE rechargeproid = ? AND profile_creator = ? LIMIT 1",array($sta,$user,$profile_creator));
	//break;

    //}
    
     
}
?>