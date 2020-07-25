<?php
include_once "../../../../engine.autoloader.php";



$id = $_REQUEST['id'];
$pos = $_REQUEST['val'];

$engine->db_query("UPDATE widget SET position = ? WHERE widgetkey = ? LIMIT 1",array($pos,$id));     

?>