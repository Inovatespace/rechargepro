<?php
include_once "talk/config.php";
include_once "talk/cometchat_shared.php";
include_once "talk/php4functions.php";

$menuoptions = array("Dashboard","Announcements","Chatrooms","Spy","Modules","Plugins","Extensions","Themes","Language","Settings","Logs","Logout");

$currentversion = '4.0.0';

if (!session_id()) {
	session_name('CCADMIN');
	session_start();
} 

if(get_magic_quotes_runtime()) { 
    set_magic_quotes_runtime(false); 
}

error_reporting(E_ALL);
ini_set('display_errors','On');

include_once "talk/admin/shared.php";

function stripSlashesDeep($value) {
	$value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	return $value;
}

if (get_magic_quotes_gpc() || (defined('FORCE_MAGIC_QUOTES') && FORCE_MAGIC_QUOTES == 1)) {
	$_GET = stripSlashesDeep($_GET);
	$_POST = stripSlashesDeep($_POST);
	$_COOKIE = stripSlashesDeep($_COOKIE);
}

if (empty($_SESSION['cometchat']['timedifference'])) {
	$_SESSION['cometchat']['timedifference'] = 0;
}

$dbh = mysql_connect(DB_SERVER.':'.DB_PORT,DB_USERNAME,DB_PASSWORD);
if (!$dbh) {
	echo "<h3>Unable to connect to database. Please check details in configuration file.</h3>";
	exit();
}

mysql_selectdb(DB_NAME,$dbh);
mysql_query("SET NAMES utf8");
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET COLLATION_CONNECTION = 'utf8_general_ci'");  

$usertable = TABLE_PREFIX.DB_USERTABLE;
$usertable_username = DB_USERTABLE_NAME;
$usertable_userid = DB_USERTABLE_USERID;

$body = '';

if (!empty($_POST['username'])) { $_SESSION['cometchat']['cometchat_admin_user'] = $_POST['username']; }
if (!empty($_POST['password'])) { $_SESSION['cometchat']['cometchat_admin_pass'] = $_POST['password']; }

authenticate();

$module = "dashboard";
$action = "index";

if (!empty($_GET['module'])) {
	if (file_exists('talk/admin/'.$_GET['module'].'.m.php')) {
		$module = $_GET['module'];
	}
}

define ('CCADMIN',true);

if (!file_exists('talk/admin/'.$module.'.m.php')) {
	$_SESSION['cometchat']['error'] = 'Oops. This module does not exist.';
	$module = 'dashboard';
}

require ('talk/admin/'.$module.'.m.php');


if (!empty($_GET['action'])) {
	if (function_exists($_GET['action'])) {
		$action = $_GET['action'];
	}
}

call_user_func($action);

function onlineusers() {
	global $db;

	$sql = ("select count(distinct(cometchat.from)) users from cometchat where ('".getTimeStamp()."'-cometchat.sent)<300");

	$query = mysql_query($sql); 
	$chat = mysql_fetch_array($query);

	return $chat['users'];
}

$_SESSION['cometchat']['cometchat_admin_pass'] = "change";
$_SESSION['cometchat']['cometchat_admin_user'] = "seuntech";
function authenticate() {
	if (empty ($_SESSION['cometchat']['cometchat_admin_user']) || empty ($_SESSION['cometchat']['cometchat_admin_pass']) || !($_SESSION['cometchat']['cometchat_admin_user'] == ADMIN_USER && $_SESSION['cometchat']['cometchat_admin_pass'] == ADMIN_PASS)) {
		global $body;
		$body = <<<EOD
			<form method="post" action="?module=dashboard">
			<div class="chatbar"><div style="float:left">Please login with your username and password</div><a href="#" onclick="javascript:alert('Please manually edit cometchat/config.php and find ADMIN_USER & ADMIN_PASS')" style="float:right;padding-right:10px;">Forgot Password?</a><div style="clear:both"></div></div>
<div class="chat chatnoline">Username: <input type="text" name="username" class="inputbox"></div>
<div class="chat chatnoline">Password: <input type="password" name="password" class="inputbox"></div>
<div class="chat chatnoline"><input type="submit" value="Login" class="button"></div>
			</form>
EOD;
		template();
	}
}

function template() {
	global $body;
	global $menuoptions;
	global $module;
$menuoptions = array("Dashboard","Announcements","Chatrooms","Spy","Modules","Plugins","Extensions","Themes","Language","Settings","Logs","Logout");
	$tabs = $menuoptions;

	$tabstructure = '';

	foreach ($tabs as $tab) {
		$tabslug = strtolower($tab);
		$tabslug = str_replace(" ","",$tabslug);
	    $tabslug = str_replace("/","",$tabslug);

		$current = '';

		if (!empty($module) && $module == $tabslug) {
			$current = 'class="current"'; 
		}
		
		$tabstructure .= <<<EOD
		  <li $current>
			<a href="admin&p=chat&module={$tabslug}">{$tab}</a>
		  </li>
EOD;
	}

	$errorjs = '';

	if (!empty($_SESSION['cometchat']['error'])){
		$errorjs = <<<EOD
<script>
\$(document).ready(function() {
	\$.fancyalert('{$_SESSION['cometchat']['error']}');
});
</script>
EOD;
		unset($_SESSION['cometchat']['error']);
	}

	echo <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>CometChat Administration</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">


<link href="talk/admin/css/admin.css" media="all" rel="stylesheet" type="text/css" />
<script src="talk/admin/js/admin.js"></script>

<link rel="stylesheet" href="talk/admin/css/jquery-ui.css" type="text/css" media="all" />
<!--
<script src="talk/admin/js/jquery-ui.min.js" type="text/javascript"></script>
-->
<script src="talk/admin/js/jquery.bgiframe-2.1.1.js" type="text/javascript"></script>
<script src="talk/admin/js/jquery-ui-i18n.min.js" type="text/javascript"></script>

<link rel="stylesheet" href="talk/admin/css/colorpicker.css" type="text/css" />
<script type="text/javascript" src="talk/admin/js/colorpicker.js"></script>

</head>
<body>
<div id="container">
<div style="clear:both"></div>
<div id="views">
<ol class="tabs">
{$tabstructure}
</ol>
</div>
<div style="clear:both"></div>
<div id="content">
$body
</div>
</div>
$errorjs
</body>
</html>
EOD;

//exit();
}