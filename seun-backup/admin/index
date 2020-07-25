<?php
//include the class file
require("engine/phprotector/PhProtector.php");
/* TESTING environment (show all PHP errors!) */
$prot= new PhProtector("log/log.xml", true); 
if($prot->isMalicious()){
	header("location: engine/errorpages/index.html");  //if an atack is found, it will be redirected to this page :)
	die();
}


include "engine.autoloader.php";
$init = "index";
$engine->authentication($init);
switch ($engine->get_session('power')) {
    
    case "superadmin":
        $uarray = array(
            "admin",
            "dashboard",
            "help",
            "setting");
        break;

    case "admin":
        $uarray = array("dashboard","help","setting");
        break;

    case "user":
        $uarray = array("dashboard","help","setting");
        break;

    default:
        $uarray = array("dashboard","help","setting");
}

$sentmenu = array_keys($engine->menukeys());
$uarray = array_merge($sentmenu, $uarray);

$request = "";
if (isset($_REQUEST['p'])) {
    $_SESSION['p'] = $engine->safe_html($_REQUEST['p']);
}
else {
    $_SESSION['p'] = "";
}


if (isset($_REQUEST['u'])) {
    if (!empty($_REQUEST['u'])) {

        if (in_array($_REQUEST['u'], $uarray)) {

            if (!empty($_REQUEST['p'])) {
                $nextpage = $engine->safe_html($_REQUEST['p']);
            }
            else {
                $nextpage = "index";
            }


            ////////////////////stsrtttttttttttttttttt check for permission
            $CONN = $engine->db();
            //check for permission on $nextpage on plugin $_REQUEST['u']
            $result = $CONN->prepare("SELECT admin_plugin.permission FROM admin_plugin JOIN plugin ON admin_plugin.pluginid = plugin.pluginid WHERE plugin.pluginkey = ? AND admin_plugin.adminid = ? LIMIT 1");
            $result->execute(array($_REQUEST['u'], $engine->get_session('adminid')));
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $fromdb = $row['permission'];
            $fromdb = explode(",", $fromdb);
            $temparray = array();
            foreach ($fromdb as $newarray) {
                if (!empty($newarray)) {
                    $explode = explode("=", $newarray);
                    $temparray[$explode[0]] = $explode[1];
                }
            }

            $fromdb = $temparray;
            $fromdb = array_keys($fromdb);
            if (in_array($nextpage, $fromdb) || $engine->get_session('power') == "superadmin" || $_REQUEST['u'] ==
                "dashboard" || $_REQUEST['u'] == "setting" || $_REQUEST['u'] == "help") {
                $enginevisibility = 1;
                if (isset($temparray[$nextpage])) {
                    $enginevisibility = $temparray[$nextpage];
                }

                if ($engine->get_session('power') == "superadmin" || $_SESSION['menu'] == "dashboard" || $_SESSION['menu'] == "setting" || $_SESSION['menu'] == "help") {
                    $enginevisibility = 1;
                }


                //if ($enginevisibility == 1) {
                    
                    if($engine->config("show_dashboard") === true){
                    $_SESSION['body'] = "plugin/".$engine->safe_html($_REQUEST['u'])."/pages/".$nextpage.".php";
                    $_SESSION['menu'] = $engine->safe_html($_REQUEST['u']);
                    }else{
                        
                    if($_REQUEST['u'] == "dashboard"){
                    $_SESSION['body'] = "engine/errorpages/404.php";
                    $_SESSION['menu'] = "";    
                    }else{
                    $_SESSION['body'] = "plugin/".$engine->safe_html($_REQUEST['u'])."/pages/".$nextpage.".php";
                    $_SESSION['menu'] = $engine->safe_html($_REQUEST['u']);   
                    }   
                        
   
                    }
                    
                    
               // }
              //  else {
                //    $_SESSION['body'] = "engine/errorpages/permission.php";
              //      $_SESSION['menu'] = $_REQUEST['u'];
              //  }

            }
            else {
                if (count($fromdb) > 0) {
                    $_SESSION['body'] = "plugin/".$engine->safe_html($_REQUEST['u'])."/pages/".$fromdb[0].".php";
                    $_SESSION['menu'] = $engine->safe_html($_REQUEST['u']);
                }
                else {
                    $_SESSION['body'] = "engine/errorpages/permission.php";
                    $_SESSION['menu'] = "";
                }
            }
            ////////////////////// enddddddddddddddddddddddddd

            //$_SESSION['body'] = "plugin/".$_REQUEST['u']."/pages/".$nextpage.".php";
            //$_SESSION['menu'] = $_REQUEST['u'];
        }
        else {
            $_SESSION['body'] = "engine/errorpages/404.php";
            $_SESSION['menu'] = "";
        }

        $request = $engine->safe_html($_REQUEST['u']);
    }
    else {
        //hereeeeeeeeeeeeeeeeee
        if($engine->config("show_dashboard") === true){
            
        $admininfo =  $engine->admin_details($engine->get_session('adminme'));    
        if($admininfo['seedashboard'] == 0){   
        $_SESSION['body'] = "plugin/dashboard/pages/index.php";
        $_SESSION['menu'] = "dashboard";
        $request = "";
        }else{
        $menutogo = $engine->first_menu();   
        if(!empty($menutogo[0])){
        $_SESSION['body'] = "plugin/$menutogo[0]/pages/$menutogo[1].php";
        $_SESSION['menu'] = "$menutogo[0]";
        $request = "";
        }else{
        $_SESSION['body'] = "engine/errorpages/401.php";
        $_SESSION['menu'] = "dashboard";
        $request = "";
        }  
        }
        
        }else{
        
        $menutogo = $engine->first_menu();   
        if(!empty($menutogo[0])){
        $_SESSION['body'] = "plugin/$menutogo[0]/pages/$menutogo[1].php";
        $_SESSION['menu'] = "$menutogo[0]";
        $request = "";
        }else{
        $_SESSION['body'] = "engine/errorpages/401.php";
        $_SESSION['menu'] = "dashboard";
        $request = "";
        }
        
        }
    }
}
else {
    
    
    
    
    
        if($engine->config("show_dashboard") === true){
            
        $admininfo =  $engine->admin_details($engine->get_session('adminme'));    
        if($admininfo['seedashboard'] == 0){   
        $_SESSION['body'] = "plugin/dashboard/pages/index.php";
        $_SESSION['menu'] = "dashboard";
        $request = "";
        }else{
        $menutogo = $engine->first_menu();   
        if(!empty($menutogo[0])){
        $_SESSION['body'] = "plugin/$menutogo[0]/pages/$menutogo[1].php";
        $_SESSION['menu'] = "$menutogo[0]";
        $request = "";
        }else{
        $_SESSION['body'] = "engine/errorpages/401.php";
        $_SESSION['menu'] = "dashboard";
        $request = "";
        }   
        }
        

        }else{
        
        $menutogo = $engine->first_menu();   
        if(!empty($menutogo[0])){
        $_SESSION['body'] = "plugin/$menutogo[0]/pages/$menutogo[1].php";
        $_SESSION['menu'] = "$menutogo[0]";
        $request = "";
        }else{
        $_SESSION['body'] = "engine/errorpages/401.php";
        $_SESSION['menu'] = "dashboard";
        $request = "";
        }
        
        }
}


$ss = new SecureSession();
$ss->check_browser = true;
$ss->check_ip_blocks = 2;
$ss->secure_word = 'IYO_';
$ss->regenerate_id = true;
if (!$ss->Check() && !$engine->config("allow_ipchange"))
{
session_destroy();
header('Location: login');
die();
}



include_once "engine_init.php"; //include "functions.php";

//$_SESSION['state'] = "Abuja";
//$_SESSION['state'] = "Enugu";
?>