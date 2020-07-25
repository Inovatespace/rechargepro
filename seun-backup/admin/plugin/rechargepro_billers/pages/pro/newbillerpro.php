<?php
include "../../../../engine.autoloader.php";


if(empty($_REQUEST['img']) || empty($_REQUEST['billername']) || empty($_REQUEST['billercode']) || empty($_REQUEST['pfieldname']) || empty($_REQUEST['ashare']) || empty($_REQUEST['category'])){
    

    echo "please fill all compulsory fields"; exit;
}


$img = $_REQUEST['img'];
$billername = $_REQUEST['billername'];
$billercode = $_REQUEST['billercode'];
$pfieldname = $_REQUEST['pfieldname'];
$ashare = $_REQUEST['ashare'];
$category = $_REQUEST['category'];
$rurl = $_REQUEST['rurl'];
$vurl = $_REQUEST['vurl'];


$row = $engine->db_query2("SELECT services_key FROM rechargepro_services WHERE services_key = ?",array($billercode));
if(!empty($row[0]['services_key'])){
    echo "Biller Code already exist"; exit;
}


$astype = $_REQUEST['astype'];
switch ($astype){
	case "Percentage":
    $what = "0";
	break;
    
	case "Fixed value":
    $what = "1";
	break;
    
	default : $what = "0";
}


if(!file_exists("../../../../tmp/".$img)){
    echo "please Selet photo"; exit;
}





$bill_secondary_field = $_REQUEST['sec'];
if (strpos($bill_secondary_field, '@') !== false) {
$ex = explode("@",$bill_secondary_field);

if(count($ex)<2){ echo "Invalid Secondary Field"; exit;}

if($ex[1] == "select"){
if (strpos($ex[2], '=') == false) { echo "Invalid Secondary Field Options"; exit;}
}

}else{
$bill_secondary_field = "";  
}
    
    
    
    
    
$bill_tertiary_field = $_REQUEST['ter'];
if (strpos($bill_tertiary_field, '@') !== false){
$ex = explode("@",$bill_tertiary_field);

if(count($ex) < 2){ echo "Invalid Tertiary Field"; exit;}

if($ex[1] == "select"){
if (strpos($ex[2], '=') == false) { echo "Invalid Tertiary Field Options"; exit;}
}

}else{
$bill_tertiary_field = "";  
}

$insertid = $engine->db_query2("INSERT INTO rechargepro_services (services_key,service_name,bill_primary_field,bill_secondary_field,bill_tertiary_field,bill_return_url,bill_verify_url,percentage,bill_formular,services_category,service_subcategory,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($billercode,$billername,$pfieldname,$bill_secondary_field,$bill_tertiary_field,$rurl,$vurl,$ashare,$what,7,$category,0));

rename('../../../../tmp/'.$img, "../../../../../theme/classic/icons/$insertid.jpg");

echo "ok"; exit;
?>