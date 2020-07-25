<?php
include "../../engine.autoloader.php";
$opts = array('http' => array('method' => "GET", 'header' => "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));
$context = stream_context_create($opts);
$file = file_get_contents('../../../zilla/report.html', false, $context);

$file = str_replace("<script>document.write(","<style>document.write(",$file);
$file = str_replace(".toLocaleDateString());</script>",".toLocaleDateString());</style>",$file);


echo $file;
?>