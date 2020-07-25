<?php
include_once "../../../../resource.php";
$CONN = $resource->sql_db();


$id = $_REQUEST['id'];
$type = $_REQUEST['type'];



if ($type == "on") {
$resultc = $CONN->prepare("UPDATE admin_widget SET status = ? WHERE adminid = ? AND id = ? LIMIT 1"); 
$resultc->execute(array(1,$resource->get_session('adminid'),$id));  
}  


if ($type == "off") {
$resultc = $CONN->prepare("UPDATE admin_widget SET status = ? WHERE adminid = ? AND id = ? LIMIT 1"); 
$resultc->execute(array(0,$resource->get_session('adminid'),$id)); 
}  
?>