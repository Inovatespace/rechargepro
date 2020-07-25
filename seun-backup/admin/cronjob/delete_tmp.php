<?php
include_once "../engine.autoloader.php";

 $folderName = "../tmp";


if (file_exists($folderName)) {
    foreach (new DirectoryIterator($folderName) as $fileInfo) {
        if ($fileInfo->isDot()) {
        continue;
        }
  
        if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= 1*24*60*60) {
           unlink($fileInfo->getRealPath());
        }
    }
}

 
 
?>