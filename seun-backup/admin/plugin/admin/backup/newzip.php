<?php
include_once "../../../engine.autoloader.php";
$filename = "backup.zip";

$source = "../../../plugin/parking_core/";
$dirlist = new RecursiveDirectoryIterator($source);
$filelist = new RecursiveIteratorIterator($dirlist);
// set script timeout value 
ini_set('max_execution_time', 5000);
// instantate object
$zip = new ZipArchive();
// create and open the archive 
if ($zip->open("$filename", ZipArchive::CREATE) !== TRUE){
    die ("Could not open archive");
}
// add each file in the file list to the archive
foreach ($filelist as $key=>$value){
    //if(realpath($key) != "C:\public\htdocs\splserver\\theme"){
               
$number =  strripos($key,"\\");
$remains = substr($key,$number,3);
if($remains != "\." && $remains != "\.."){
    $newfile = str_ireplace("../","",$key);
    $zip->addFile($key, $newfile) or die ("ERROR: Could not add file: $newfile");
    //echo $key."-".$remains."<br />";
  }  
    
    //}
}
$zip->close();
?>

<div style="float: left;">Archiving done do you want to start transfer to back up server  {<?php echo filesize($filename);?>}</div>
<div onclick="transferzip()" class="shadow greenmenu" style="float: right; padding:3px 10px; margin:3px; cursor:pointer;">Transfer Now</div>