<?php
include_once "../../../../engine.autoloader.php";
$CONN = $engine->db();


$id = $_REQUEST['id'];
$left = $_REQUEST['left'];
$top = $_REQUEST['top'];


$resultc = $CONN->prepare("UPDATE admin_widget SET widgettop = ?, widgetleft = ? WHERE id = ? LIMIT 1"); 
$resultc->execute(array($top,$left,$id));     
?>