<?php
include_once "../../../../engine.autoloader.php";


$type = $_REQUEST['type'];
$id = $_REQUEST['id'];
$pos = $_REQUEST['pos'];



if($type == "down"){
$newpos = $pos + 1;

$engine->db_query("UPDATE widget SET widgetorder = widgetorder - ? WHERE widgetorder = ? LIMIT 1",array(1,$newpos)); 
$engine->db_query("UPDATE widget SET widgetorder = widgetorder + ? WHERE widgetid = ? LIMIT 1",array(1,$id));       
}


if($type == "up"){
$newpos = $pos - 1;

$engine->db_query("UPDATE widget SET widgetorder = widgetorder + ? WHERE widgetorder = ? LIMIT 1",array(1,$newpos)); 
$engine->db_query("UPDATE widget SET widgetorder = widgetorder - ? WHERE widgetid = ? LIMIT 1",array(1,$id)); 
   
}
?>