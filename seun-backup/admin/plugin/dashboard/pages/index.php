<?php
$engine = new engine();


if ($engine->config("dashboard_format") == 1) {
    include "dashboard.php";
} else {

$opts = array('http' => array('method' => "GET", 'header' =>
            "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));


    if (!file_exists("theme/" . $engine->config("theme") . "/blog.php")) {

        $file = "plugin/dashboard/pages/blog.php";

        $context = stream_context_create($opts);
        $file = file_get_contents($file, false, $context);
        $themeparameters = array_merge_recursive($engine->theme_parameter("blog"), array
            ());
        $themeparameterskey = array_keys($themeparameters);
        $themeparametersvalue = array_values($themeparameters);
        $file = str_replace($themeparameterskey, $themeparametersvalue, $file);
        echo $file;


    } else {
        $file = "theme/" . $engine->config("theme") . "/blog.php";

        ob_start();
        include ($file);
        $output = ob_get_contents();
        ob_end_clean();
        
            
            
$themeparameters = array_merge_recursive($engine->theme_parameter("blog"), array());
$themeparameterskey = array_keys($themeparameters);
$themeparametersvalue = array_values($themeparameters);
$file = str_replace($themeparameterskey, $themeparametersvalue, $output);
            
            echo $file;

    }


}
?>
 
