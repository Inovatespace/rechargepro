<?php
include_once "../../../engine.autoloader.php";
ini_set('max_execution_time', 5000);
ini_set('memory_limit', '464M');
if(isset($_REQUEST["array"])){
$backup = $_REQUEST["array"];
$fileexplode = explode(",",$backup);

foreach($fileexplode AS $tmpcource){
if(!empty($tmpcource)){
    
    if($tmpcource != "backup_database"){
$location = $engine->backup_location(2);
$filename = "../../../tmp/$tmpcource.zip";
    
$source = "../../../".$location[$tmpcource];
$dirlist = new RecursiveDirectoryIterator($source);
$filelist = new RecursiveIteratorIterator($dirlist);
// set script timeout value 
// instantate object
$zip = new ZipArchive();
// create and open the archive 
if ($zip->open("$filename", ZipArchive::CREATE) !== TRUE){
    die ("Could not open archive");
}
// add each file in the file list to the archive
foreach ($filelist as $key=>$value){   
$number =  strripos($key,"\\");
$remains = substr($key,$number,3);
if($remains != "\." && $remains != "\.."){
    $newfile = str_ireplace("../","",$key);
    $zip->addFile($key, $newfile) or die ("ERROR: Could not add file: $newfile");
    //echo $key."-".$remains."<br />";
  }  
    
    }
    
    $zip->close();
}else{
    $dumpSettings = array(
    'compress' => Mysqldump::NONE,//
    'no-data' => false,
    'add-drop-table' => true,
    'single-transaction' => true,
    'lock-tables' => true,
    'add-locks' => true,
    'extended-insert' => false,
    'disable-keys' => true,
    'skip-triggers' => false,
    'add-drop-trigger' => true,
    'databases' => false,
    'add-drop-database' => false,
    'hex-blob' => true,
    'no-create-info' => false,
    'where' => ''
    );
        
$dump = new Mysqldump($dumpSettings);

$dump->start("../../../tmp/backup_database.sql");  
$zip = new ZipArchive();
// create and open the archive 
if ($zip->open("../../../tmp/backup_database.zip", ZipArchive::CREATE) !== TRUE){
    die ("Could not open archive");
}
$zip->addFile("../../../tmp/backup_database.sql", "database.sql") or die ("ERROR: Could not add file: $newfile");
$zip->close();
@unlink("../../../tmp/backup_database.sql");
}
}
}
}
?>
Done
<script type="text/javascript">
jQuery(document).ready(function($){
  window.location.reload();
    })
</script>
