<?php
if(isset($_REQUEST['u'])){
$file = $engine->config("theme_folder") . $engine->config("theme") . "/pages/" . $_REQUEST['u'] .".php";
if (!file_exists($file)) {
    header('HTTP/1.0 404 Not Found', true, 404);
    }
    

}
?>