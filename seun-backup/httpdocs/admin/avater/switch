<?php
include ('engine.autoloader.php');
if (!isset($_REQUEST['u']))
{
    unset($_SESSION["tmpuser"]);
    header("location:admin");
} else
{
    $_SESSION["tmpuser"] = $_REQUEST['u'];
    header("location:index");
}
?>