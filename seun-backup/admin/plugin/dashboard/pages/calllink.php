<?php
require "../../../engine.autoloader.php";
$link = $_REQUEST['link'];
$explode = explode("/",$link);
ob_start();

include "../../../".$link;

$output = ob_get_contents();
$output = $engine->get_widgetlanguage("../../../widget/$explode[1]/language/", $output);
ob_end_clean();
echo $output; 
?>
 

