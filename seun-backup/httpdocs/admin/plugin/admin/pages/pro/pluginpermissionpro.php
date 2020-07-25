<?php
include_once "../../../../engine.autoloader.php";



$adminid = $_REQUEST['adminid'];
$pluginid = $_REQUEST['pluginid'];



$row = $engine->db_query("SELECT permission FROM admin_plugin WHERE adminid = ? AND pluginid = ? LIMIT 1",array($adminid,$pluginid));
$permission = $row[0]['permission'];
$thepermission = "";
if(isset($_REQUEST['visibility'])){
for($i=0;$i<count($_REQUEST['visibility']);$i++){
$themenu = $_REQUEST['visibility'][$i];
$thepermission = $thepermission.",$themenu=0";
}
$engine->db_query("UPDATE admin_plugin SET permission = ? WHERE adminid = ? AND pluginid = ? LIMIT 1",array($thepermission,$adminid,$pluginid));
}


if(isset($_REQUEST['admin'])){
for($i=0;$i<count($_REQUEST['admin']);$i++){
$themenu = $_REQUEST['admin'][$i];
$thepermission = str_ireplace("$themenu=0","$themenu=1",$thepermission); 
$thepermission = str_ireplace("$themenu=2","$themenu=1",$thepermission); 

$engine->db_query("UPDATE admin_plugin SET permission = ? WHERE adminid = ? AND pluginid = ? LIMIT 1",array($thepermission,$adminid,$pluginid));
}
}


if(isset($_REQUEST['download'])){
for($i=0;$i<count($_REQUEST['download']);$i++){
$themenu = $_REQUEST['download'][$i];
$thepermission = str_ireplace("$themenu=0","$themenu=2",$thepermission); 
$thepermission = str_ireplace("$themenu=1","$themenu=2",$thepermission); 

$engine->db_query("UPDATE admin_plugin SET permission = ? WHERE adminid = ? AND pluginid = ? LIMIT 1",array($thepermission,$adminid,$pluginid));
}
}


echo "<meta http-equiv='refresh' content='0;url=../../../../admin&p=index&i=$adminid'>"; exit;
?>