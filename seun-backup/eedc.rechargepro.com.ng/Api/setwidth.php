<?php
include_once "engine.autoloader.php";
if(isset($_REQUEST['width'])) {   
$_SESSION['rs'] =  htmlentities($_REQUEST['width']);
if(isset($_SESSION['rs'])){
echo "1"; exit;
}
}
?>
