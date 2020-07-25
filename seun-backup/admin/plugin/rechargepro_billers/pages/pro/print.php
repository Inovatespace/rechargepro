<?php include "../../../../engine.autoloader.php";?>
<style type="text/css">
#content_body{background: url(theme/classicnext/images/innerbg.png) center repeat;}
</style>
<?php
if(!isset($_REQUEST['id'])){
   exit; 
    }
    
    

$id = htmlentities($_REQUEST['id']);

$row = $engine->db_query2("SELECT transactionid,rechargepro_service,rechargepro_subservice,account_meter,business_district,thirdPartycode,address,name,phcn_unique,amount,phone,email,payment_method,transaction_status,transaction_code,transaction_reference,rechargepro_status,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ?",array($id));
	
if(empty($row[0]['transactionid'])){
exit;
}


$transactionid = $row[0]['transactionid'];
$rechargepro_service = $row[0]['rechargepro_service'];
$rechargepro_subservice = $row[0]['rechargepro_subservice'];
$account_meter = $row[0]['account_meter'];
$business_district = $row[0]['business_district'];
$thirdPartycode = $row[0]['thirdPartycode'];
$address = $row[0]['address'];
$name = $row[0]['name'];
$phcn_unique = $row[0]['phcn_unique'];
$amount = $row[0]['amount'];
$phone = $row[0]['phone'];
$email = $row[0]['email'];
$payment_method = $row[0]['payment_method'];
$transaction_status = $row[0]['transaction_status'];
$transaction_code = $row[0]['transaction_code'];
$transaction_reference = $row[0]['transaction_reference'];
$rechargepro_status = $row[0]['rechargepro_status'];
$rechargepro_status_code = $row[0]['rechargepro_status_code'];
$rechargepro_print = $row[0]['rechargepro_print'];
$transaction_date = $row[0]['transaction_date'];


?>

  

<div style="">
<div style="text-align: right; font-size: 180%;"><a class="fas fa-print"onclick="javascript:Print('printDivContent');"></a></div>


<div id="printDivContent" style="padding:10px;" class="profilebg">

<?php
if($rechargepro_status_code == "1"){
?>
<div style="font-size: 150%;">Thank you for using rechargepro Pay</div>
<div style="font-size: 150%; margin-bottom: 30px;">An Electronic copy has been sent to your email if provided</div>


<div class="nextcolor" style="text-align: center; font-size:25px; margin-bottom: 20px; margin-top:20px; font-weight:bold;">INVOICE</div>
<?php
	}else{
	   ?>
       
       <div class="nWarning"><?php echo $transaction_status;?><br />Please Contact the Administrator</div>
       <?php
	}
?>


<div style="text-align: left; line-height: 20px;">
<?php

$response = json_decode($rechargepro_print, true);
function return_value($value){
    if(!is_array($value)){
    return ucwords($value);
    }else{
     foreach($value AS $valuea => $valueb){
        return ", $valuea : $valueb";
        }
    }
    
    }

function return_name($key,$value){
    
if(!is_array($key)){
    
$key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);
if(!empty($value)){
  return '<div  style="overflow: hidden;">
    <div style="float: left; font-weight:bold;">'.ucwords($key).': </div>
    <div style="float: left;">'.return_value($value).'</div>
    </div>';
    }
    
}else{
    
foreach($keya AS $keyb => $valueb){
    $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
    return return_name($keyb,$valueb);
    } 
}

}




if(isset($response['details'])){
foreach($response['details'] AS $key => $value){
    
    if(!is_array($key)){
        
$key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);

echo return_name($key,$value);

}else{
foreach($key AS $keya => $valuea){
    
if(!is_array($keya)){
$keya = preg_replace('/([A-Z])(?<!^)/', ' $1', $keya);
echo return_name($keya,$valuea);
}else{
    
foreach($keya AS $keyb => $valueb){
    $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
    echo return_name($keyb,$valueb);
    }
    
}



    }
}

}
}






if(isset($response['VendorReference'])){
foreach($response AS $key => $value){
    
    if(!is_array($key)){
        
$key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);

echo return_name($key,$value);

}else{
foreach($key AS $keya => $valuea){
    
if(!is_array($keya)){
$keya = preg_replace('/([A-Z])(?<!^)/', ' $1', $keya);
echo return_name($keya,$valuea);
}else{
    
foreach($keya AS $keyb => $valueb){
    $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
    echo return_name($keyb,$valueb);
    }
    
}



    }
}

}
}
?>
</div>

<script type="text/javascript">
function isChrome() {
  var isChromium = window.chrome,
    winNav = window.navigator,
    vendorName = winNav.vendor,
    isOpera = winNav.userAgent.indexOf("OPR") > -1,
    isIEedge = winNav.userAgent.indexOf("Edge") > -1,
    isIOSChrome = winNav.userAgent.match("CriOS");

  if (isIOSChrome) {
    return true;
  } else if (
    isChromium !== null &&
    typeof isChromium !== "undefined" &&
    vendorName === "Google Inc." &&
    isOpera === false &&
    isIEedge === false
  ) {
    return true;
  } else { 
    return false;
  }
}

function Print(elementId)
{
data = $("#"+elementId).html();
    var mywindow = window.open();
    var is_chrome = Boolean(mywindow.chrome);
    mywindow.document.write(data);

   if (is_chrome) {
     setTimeout(function() { // wait until all resources loaded 
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        mywindow.print(); // change window to winPrint
        mywindow.close(); // change window to winPrint
     }, 250);
   } else {
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();
   }

    return true;
}

</script>

</div>
</div>