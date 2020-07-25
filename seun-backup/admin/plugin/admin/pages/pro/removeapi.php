<?php
include_once "../../../../engine.autoloader.php";


$id = $_REQUEST['id'];

$engine->db_query("DELETE FROM api WHERE id = ? LIMIT 1",array($id)); 


?>