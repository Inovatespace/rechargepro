<?php
include('code128.class.php');
function dome($staffid){
$barcode = new phpCode128($staffid, 120, false, 18);//'c:\windows\fonts\verdana.ttf'
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

$staffid = $_REQUEST['id'];
dome($staffid);
echo "<img src='parking/$staffid.png' height='80'>";
?>