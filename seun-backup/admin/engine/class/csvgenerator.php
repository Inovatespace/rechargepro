<?php
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=range.csv");
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen("php://output", "w");
$csv = urldecode($_REQUEST['csv']);
$csv = str_replace('""', '";"', $csv);
$count = $_REQUEST['count'];
$sn = 0;
$explode = explode(";",$csv);
foreach($explode AS $value){
$sn++;
$line = "";
$comma = "";



$line .= $comma.$value.',';



if($sn == $count){
$line .= "\n";
 $sn = 0;
}
 fputs($fp, $line);   
  
}





fclose($fp);
?>