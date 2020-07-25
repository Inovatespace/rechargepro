<?php

function pdbg($data, $color="orange", $Line=null, $File=null, $height=180, $width=800) {
	$dbg = debug_backtrace();
	print "<div style=\"width:".$width."px;float:left;margin:5px\">";
	print "<div style=\"border:1px solid #999;font-size:11px;\">";
	print "<div style=\"background-color:".$color.";padding:2px 5px;font-weight:bold;border-bottom:1px solid #999;\">";
	print $File;
	if($Line) print', LINE: '.$Line.' ';
	$offset = (isset($dbg[1])) ? 1:0;
	if($offset>0)
	print $dbg[$offset]["class"].$dbg[$offset]["type"].$dbg[$offset]["function"]."(".count( $dbg[$offset]["args"]).")";
	print "</div>";
	print "<textarea style=\"width:100%;height:".$height."px;border:none;padding:0 0 0 5px;font-size:11px\">";
	print_r($data);
	print "</textarea></div>";
	print "</div>";
}

ini_set("max_execution_time",3);

include_once("checksum.class.php");



// begin success
$checksum = Checksum::build("Controller");
$proof = Checksum::proof($checksum,"Controller");
pdbg($checksum, "lime", __LINE__,__FILE__,20);
pdbg(($proof==true?"valid checksum":"invalid checksum"), "orange", __LINE__,__FILE__,20);
// end success

exit;

// begin time failure
$checksum = Checksum::build("Controller","list","someParameter");
$proof = Checksum::proof("aeeb089b00ab1d9934f1f065498c7497479cbd12","Controller","list","someParameter");
pdbg($checksum, "lime", __LINE__,__FILE__,20);
pdbg(($proof==true?"valid checksum":"invalid checksum"), "orange", __LINE__,__FILE__,20);
// end time failure



// begin parameter failure
$checksum = Checksum::build("Controller","list","someParameter");
$proof = Checksum::proof($checksum,"Controller","list","somethingElse");
pdbg($checksum, "lime", __LINE__,__FILE__,20);
pdbg(($proof==true?"valid checksum":"invalid checksum"), "orange", __LINE__,__FILE__,20);
// end time failure


// begin range
//Checksum::setTimestamp("2008-02-19");
//Checksum::setValidTimeRange(3600*24*665);
Checksum::isValidFromTo("2008-01-19","2009-03-19");
$checksum = Checksum::build("Controller","list","asdsd");
pdbg($checksum, "orange", __LINE__,__FILE__,20);
$proof = Checksum::proof($checksum, "Controller","list","asdsd");
pdbg($proof, "orange", __LINE__,__FILE__,20);
// end range




// begin range failed
Checksum::isValidFromTo("2008-01-19","2009-01-19");
$checksum = Checksum::build("Controller","list","asdsd");
pdbg($checksum, "orange", __LINE__,__FILE__,20);
$proof = Checksum::proof($checksum, "Controller","list","asdsd");
pdbg($proof, "orange", __LINE__,__FILE__,20);
// end range