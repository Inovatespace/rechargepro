<?php
include('../engine.autoloader.php');
include('code128.class.php');

$CONN = $engine->db();

function dome($staffid){
$barcode = new phpCode128($staffid, 120, false, 18);
$barcode->setShowText(false);
$barcode->setPixelWidth(2);
$barcode->setBorderWidth(0);
$barcode->setBorderSpacing(0);
$barcode->setEanStyle(false);
$barcode->setTextSpacing(20);
$barcode->setAutoAdjustFontSize(true);
$barcode->saveBarcode("parking/$staffid.png");
//echo "<img src='parking/$staffid.png'>";
//echo "<div style='font-size:35px;'>".$staffid."</div>";
}

$resultmail = $CONN->prepare( "SELECT staffid FROM members WHERE active = ?"); 
$resultmail->execute(array('1')); 
while($rowmail = $resultmail->fetch(PDO::FETCH_ASSOC)) {
$staffid = $rowmail['staffid'];
dome($staffid);
}
?>