<?php
include_once "../../../../engine.autoloader.php";



$id = $_REQUEST['id'];
$username = $_REQUEST['username'];


$engine->db_query("DELETE FROM admin WHERE adminid = ? LIMIT 1",array($id)); 

$engine->db_query("DELETE FROM admin_plugin WHERE adminid = ?",array($id)); 


$engine->db_query("DELETE FROM admin_widget WHERE adminid = ?",array($id)); 


if(file_exists("../../../../avater/".$username.".jpg")){
    unlink("../../../../avater/".$username.".jpg");
    unlink("../../../../avater/small_".$username.".jpg");
}
?>