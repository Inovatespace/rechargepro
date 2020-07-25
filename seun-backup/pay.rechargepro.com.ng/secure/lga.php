<?php
include ('../engine.autoloader.php');

$state = $_REQUEST['state'];

if(file_exists("lga/".$state.".txt")){
    
    $ln = '<select  class="input" id="lga" style="width: 97%; padding:10px 1%;">';
    
$handle = fopen("lga/".$state.".txt", "r");

if ($handle) {
    
    while (($line = fgets($handle)) !== false) {
        $ln .= "<option>$line</option>";
    }

    fclose($handle);
} else {
    // error opening the file.
}
  
}
  
$ln .= '</select>';

echo $ln;
?>