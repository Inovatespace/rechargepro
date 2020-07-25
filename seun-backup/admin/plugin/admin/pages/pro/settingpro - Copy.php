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

$widgetstate = "
'user_widget_state_change'=>$allow_widgetstate,
";

$webconfig = "
'website_root'=>'$websitelocation',
'author'=>'$companyname',
'app_name'=>'$appname',       
'admin_email'=>'$email',
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

configeditor('SITEWIDTH CONFIGURATION', $site_width);
configeditor('WEBSITE CONFIGURATION', $webconfig);
configeditor('DATABASE CONFIGURATION', $dbconfig);
configeditor('LOG CONFIGURATION', $logconfig);
configeditor('USERWIDGET CONFIGURATION', $widgetstate);
configeditor('ENDING CONFIGURATION', ";");
function configeditor($keyword, $config)
{

    $file = "../../../../config/config.php";


    $fh = fopen($file, 'r');
    $data = fread($fh, (filesize($file)+1000));
    fflush($fh);
    fclose($fh);

    $pattern = "/\/\* $keyword START \*\/(\s*)(.*?)(\s*)\/\* $keyword END \*\//is";
    $replacement = "/* $keyword START */".$config."/* $keyword END */";


    $newdata = preg_replace($pattern, $replacement, $data);
    
    

    if (is_writable($file)) {
        if (!$handle = fopen($file, 'w')) {
            echo "Cannot open file ($file)";
            exit;
        }

        if (fwrite($handle, $newdata) === false) {
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