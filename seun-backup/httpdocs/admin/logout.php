<?php
include ('engine.autoloader.php');
$Cookie = new Cookie();
$Cookie->delete("header");
session_destroy();
header("location:login"); exit;
?>