<?php

define('MENUAUTH_DOCROOT', '1');
define('AUTH_DOCROOT', '1');

$websitelocation = $_REQUEST['websitelocation'];
$email = $_REQUEST['email'];
$companyname = $_REQUEST['companyname'];
$appname = $_REQUEST['appname'];
$sitewidth = trim($_REQUEST['sitewidth']);
$sitewidthunit = trim($_REQUEST['sitewidthunit']);
$sitekey = $_REQUEST['sitekey'];
$dbhost = $_REQUEST['dbhost'];
$dbname = $_REQUEST['dbname'];
$dbuser = $_REQUEST['dbuser'];
$dbpassword = $_REQUEST['dbpassword'];
$logerror = $_REQUEST['logerror'];
$displayerror = $_REQUEST['displayerror'];
$allow_widgetstate = $_REQUEST['allow_widgetstate'];
$user_edit_information = $_REQUEST['user_edit_information'];
$user_change_image = $_REQUEST['user_change_image'];
$mobile = $_REQUEST['mobile'];
$website = $_REQUEST['website'];
$show_dashboard = $_REQUEST['show_dashboard'];
$dashboard_size = $_REQUEST['dashboard_size'];
$dashboard_format = $_REQUEST['dashboard_format'];
$allow_user_theme_sellection = $_REQUEST['allow_user_theme_sellection'];
$allow_ipchange = $_REQUEST['allow_ipchange'];

$showdash = "
'show_dashboard'=>$show_dashboard,
'dashboard_size'=>'$dashboard_size',
'dashboard_format'=>'$dashboard_format',//1==dashboard,2=blog
'allow_user_theme_sellection'=>$allow_user_theme_sellection,
'allow_ipchange'=>$allow_ipchange,
";

$widgetstate = "
'user_widget_state_change'=>$allow_widgetstate,
";

$webconfig = "
'website_root'=>'$websitelocation',
'author'=>'$companyname',
'app_name'=>'$appname',       
'admin_email'=>'$email',
'admin_mobile'=>'08026633096',
'admin_website'=>'www.guotransport.com',
";


$dbconfig = "
'user_key'=>'$sitekey',
'database_dsn' => 'mysql:dbname=$dbname; host=$dbhost;',
'database_user' => '$dbuser',
'database_pass' => '$dbpassword',
";

$logconfig = "
'log_error' => $logerror, // TRUE or FALSE
'display_error' => $displayerror, // TRUE or FALSE
";

$site_width = "
'site_width'=>'width:".$sitewidth."$sitewidthunit', 
";

$usereditimage = "
'user_change_image'=>$user_change_image,
";

$usereditprofile = "
'user_edit_information'=>$user_edit_information,
";




$configarray = array('DASHBOARD CONFIGURATION'=>$showdash,'USERIMAGE CONFIGURATION'=>$usereditimage,'USEREDIT CONFIGURATION'=>$usereditprofile,'SITEWIDTH CONFIGURATION'=>$site_width,'WEBSITE CONFIGURATION'=>$webconfig,'DATABASE CONFIGURATION'=>$dbconfig,'LOG CONFIGURATION'=>$logconfig,'USERWIDGET CONFIGURATION'=>$widgetstate,'ENDING CONFIGURATION'=>";");

configeditor($configarray);
function configeditor($configarray)
{

    $file = "../../../../config/config.php";


    $fh = fopen($file, 'r');
    $data = fread($fh, (filesize($file)+1000));
    fflush($fh);
    fclose($fh);



foreach($configarray AS $keyword => $config){
    $pattern = "/\/\* $keyword START \*\/(\s*)(.*?)(\s*)\/\* $keyword END \*\//is";
    $replacement = "/* $keyword START */".$config."/* $keyword END */";
    $data = preg_replace($pattern, $replacement, $data);
}
    
    
    

    if (is_writable($file)) {
        if (!$handle = fopen($file, 'w')) {
            echo "Cannot open file ($file)";
            exit;
        }

        if (fwrite($handle, $data) === false) {
            echo "Cannot write to file ($file)";
            exit;
        }
fflush($handle);
        fclose($handle);

    }
    else {
        echo "The file $file is not writable. Please CHMOD config.php to 777.";
        exit;
    }


}

echo "<meta http-equiv='refresh' content='0;url=../../../../admin&p=setting'>";

?>