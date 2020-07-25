<?php
include_once "../../../../engine.autoloader.php";



$id = $_REQUEST['id'];


$engine->db_query("UPDATE widget SET widgetstatus = ? WHERE widgetid = ? LIMIT 1",array(1,$id)); 
    

?>