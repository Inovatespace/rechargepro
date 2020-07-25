<script type="text/javascript">
function call_page(Id){
    window.location.href = "/index#"+Id;
}
</script>
<link rel="stylesheet" type="text/css" href="/java/lightbox/themes/default/jquery.lightbox.css" />
<!--[if IE 6]>
<link rel="stylesheet" type="text/css" href="js/lightbox/themes/default/jquery.lightbox.ie6.css" />
<![endif]-->

<script type="text/javascript" src="/java/lightbox/jquery.lightbox.min.js"></script>
<script type="text/javascript">
  jQuery(document).ready(function($){
    $('.lightbox').lightbox();
  });
</script>
<?php
$engine = new engine();

if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        echo "<div style='color:white; font-size:150%; text-align:center; margin-top:50px;'>this Account is not authorised for this action</div>"; exit;
    }
}


$id = $engine->get_session("cartid");
$row = $engine->db_query("SELECT quickpayid,service_charge,quickpay_status_code,quickpay_subservice,quickpay_service,account_meter,amount FROM quickpay_transaction_log WHERE transactionid = ? LIMIT 1",array($id)); 


$service_charge = $row[0]['service_charge'];
$mainamont = $row[0]['amount'];
$quickpayid = $row[0]['quickpayid'];
$amont = $mainamont;

$quickpay_subservice = $row[0]['quickpay_subservice'];
$quickpay_status_code = $row[0]['quickpay_status_code'];


if($quickpay_status_code == 1){
echo "<meta http-equiv='refresh' content='0;url=invoice&id=".$quickpayid."_".$id."'>"; exit;
}

$total = $amont;
$rowb = $engine->db_query("SELECT services_category FROM quickpay_services WHERE services_key = ? LIMIT 1",array($quickpay_subservice));

$category = $rowb[0]['services_category'];

$special = ""; 
$specialtext = ""; 
if(!in_array($category,array(2,3,4,7))){
if($engine->get_session("quickpayrole")){

if($engine->get_session("quickpayrole") > 3){
   $special = "&#x20A6 100";
   $specialtext = ""; 
   $total = $amont+100;
}
}else{
$special = "&#x20A6 100";
$total = $amont+100;
}
}

if($service_charge > 0){
  $special = "&#x20A6 $service_charge";  
}
$total = $total+$service_charge;



$cardview = "";
$display1  = "";
$display2  = "";
$display3  = "PHONE/ACCOUNT";
switch ($category){
    case "1":
$display1  = "<span style='color:black;'>Customer Name</span> ";
$display2  = "<span style='color:black;'>Address</span>";
$display3  = "<span style='color:black;'>Meter Number</span>";
	break;
    
    case "2":
    $display1  = "<span style='color:black;'></span> ";
    if(isset($_REQUEST['detail1'])){
        if(!empty($_REQUEST['detail1'])){
        $display1  = "<span style='color:black;'>Customer Name</span> ";    
        }
    }

$display2  = "";
$display3  = "<span style='color:black;'>Line to credit</span>";
	break;
    
    case "3":
$display1  = "<span style='color:black;'>Bundle</span>";
$display2  = "";
$display3  = "<span style='color:black;'>Line to credit</span>";
	break;
    
    
    case "5":
$display1  = "<span style='color:black;'>Customer Name</span>";
$display2  = "<span style='color:black;'>Account Number</span>";
$display3  = "<span style='color:black;'>Smart card Number</span>";
	break;
}



switch ($quickpay_subservice){
case "BANK TRANSFER":
$display1  = "<span style='color:black;'>Account Name</span> ";
$display3  = "<span style='color:black;'>Account Number</span>";
$total = $amont+35;
$special = "&#x20A6 35";
if($amont > 30000){
  $total = $amont+40;  
  $special = "&#x20A6 40";
}



$cardview = "display:none;";
	break;
    
 case "TRANSFER":
$display1  = "<span style='color:black;'>quickpay Name</span> ";
$display2  = "<span style='color:black;'>quickpay Number</span>";
$display3  = "";
$cardview = "display:none;";
	break;
}

$semail = "guest@user.com";
if($engine->get_session("quickpayemail")){
  $semail = $engine->get_session("quickpayemail");  
}
?>


<script type="text/javascript" src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
<script type="text/javascript">
function payment_method(methodofpayment,amount){
    
$("#loadingarea").html('<i class="fa fa-spinner fa-spin"></i> Loading');

var amountopay = parseInt("<?php echo $total;?>");
var datatosend = "paymentmethod="+encodeURIComponent(methodofpayment);




$.ajax({
    type: "POST",
    url: "/theme/classic/pages/call/paymentmethod.php",
    data: datatosend,
    cache: false,
    success: function (html){
        
    $("#loadingarea").html('');
    var arr = html.split('@');
    
    if(arr[0].trim() == "bad"){
    $.alert(arr[1]); return false;
    }
    
    if(arr[0].trim() == "ok"){
        
     if(methodofpayment == 1){
        
        var pref = "internet_"+arr[1];
       //call web pay
       //say thank you + online receipt
         getpaidSetup({
        	customer_email:"<?php echo $semail;?>",
        	amount:amountopay,
            currency: "NGN",
            custom_logo:"https://quickpay.com.ng/theme/classic/images/logo.png",
        	txref:pref,
        	PBFPubKey:"<?php echo $engine->config('rave_public_key');?>",
        	meta:[{ flightid:3849 }],
        	onclose:function(){},
        	callback:function(response){
            flw_ref = response.tx.flwRef;//chargeResponse = response.tx.chargeResponseCode;
            window.location.href="pay/cardpayment.php?flw_ref="+flw_ref+"&id="+arr[1]; 
        	}
        });
     }
        
     if(methodofpayment == 2){
    //if not login tell login
    //if login -> show pay
    //say thank you + online receipt
 //jQuery.fn.calllink("/theme/classic/pages/call/walletpayment.php?width=400");
 
  $.lightbox("/theme/classic/pages/call/walletpayment.php", {
        'width'       : 400,
        'height'      : 270,
        'autoresize'  : true
      });
      
      
     }
     

      
    }
    
 

    }
    
    
    });   
    
};
</script>


<div class="sitewidth" style="margin-right:auto; margin-left:auto; margin-top:150px; overflow: hidden;">










<style type="text/css">
.btt{padding:20px 2px 20px 2px;  border-bottom: 1px dashed #CCCCCC; }
</style>

<div style="font-size: 180%;">Please confirm your details below</div>

<div style="margin:3px; background-color:white; color:black; padding: 20px;">

<div class="btt">
<div style="float: left; width:30%;">Product</div><div style="float:left; 68%"><?php echo $row[0]['quickpay_service'];?></div> 
</div>


<?php if(!empty($display1)){;?>
<div class="btt" id="info"><div style="float: left; width:30%;"><?php echo $display1;?></div><div style="float:left; 68%"><?php echo $engine->safe_html($_REQUEST['detail1']);?></div></div>
<?php 	}?>

<?php if(!empty($display2)){;?>
<div class="btt" id="info"><div style="float: left; width:30%;"><?php echo $display2;?></div><div style="float:left; 68%"><?php echo $engine->safe_html($_REQUEST['detail2']);?></div></div>
<?php 	}?>

<?php if(!empty($display3)){;?>
<div class="btt"><div style="float: left; width:30%;"><?php echo $display3;?></div> <div style="float:left; 68%"><span id="meterc"><?php echo $row[0]['account_meter'];?></span></div></div>
<?php 	}?>

<div class="btt"><div style="float: left; width:30%;">SUB TOTAL</div><div style="float:left; 68%">&#x20A6<span id="amountc"><?php echo $amont;?></span></div></div>

<?php if(!empty($special)){?> 
<div class="btt"><div style="float: left; width:30%;">SERVICE CHARGE</div><div style="float:left; 68%"><?php echo $special;?></div></div>
<?php }?>

<div class="btt"><div style="float: left; width:30%;">TOTAL</div> <div style="float:left; 68%">&#x20A6<span id="amountc"><?php echo $total;?></span></div></div>




<div id="loadingarea"></div>
<div style="overflow: hidden; margin-top:20px;">
<div style="float: left; margin-right:20px;">
<div style="margin-bottom: 10px;">Pay with credit card</div>
<div><img src="/theme/classic/images/card.png" onclick="payment_method('1','<?php echo $total;?>')" style="<?php echo $cardview;?> cursor:pointer; width: 200px; float:left;" /></div></div>


<div style="float: left;">
<div style="margin-bottom: 10px;">Pay from wallet </div>
<div><img src="/theme/classic/images/waller.png" onclick="payment_method('2','<?php echo $total;?>')" style="cursor:pointer; width: 150px;" /></div></div>




</div>
</div>






</div>