<?php


//require dirname(dirname(dirname(dirname(__file__))))."/engine.autoloader.php";
if(class_exists('engine')){
$host = self::config("database_dsn2");
$users = self::config("database_user2");
$pase = self::config("database_pass2");
}else{
require dirname(dirname(dirname(dirname(__file__))))."/engine.autoloader.php";
$host = $engine->config("database_dsn2");
$users = $engine->config("database_user2");
$pase = $engine->config("database_pass2");   
}


$hb = explode("=",$host);
$h = explode(";",$hb[2]);
$host = $h[0];

$h = explode(";",$hb[1]);
$dbb = $h[0];
//'mysql:dbname=mcbpay_admin; host=localhost;'

	define("DB_HOST", $host);
	define("DB_UID", $users);
	define("DB_PWD", $pase);
	define("DB_DATABASE", $dbb);

?>