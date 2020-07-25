<?php
$engine = new engine();

if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        echo "<div style='color:white; font-size:150%; text-align:center; margin-top:50px;'>this Account is not authorised for this action</div>"; exit;
    }
}


$id = $engine->get_session("cartid");
$row = $engine->db_query("SELECT rechargeproid,service_charge,rechargepro_status_code,rechargepro_subservice,rechargepro_service,account_meter,amount FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($id)); 


$service_charge = $row[0]['service_charge'];
$mainamont = $row[0]['amount'];
$rechargeproid = $row[0]['rechargeproid'];
$amont = $mainamont;

$rechargepro_subservice = $row[0]['rechargepro_subservice'];
$rechargepro_status_code = $row[0]['rechargepro_status_code'];


if($rechargepro_status_code == 1){
echo "<meta http-equiv='refresh' content='0;url=invoice&id=".$rechargeproid."_".$id."'>"; exit;
}

$total = $amont;
$rowb = $engine->db_query("SELECT services_category FROM rechargepro_services WHERE services_key = ? LIMIT 1",array($rechargepro_subservice));

$category = $rowb[0]['services_category'];

$special = ""; 
$specialtext = ""; 
if(!in_array($category,array(2,3,4,7))){
if($engine->get_session("rechargeprorole")){

if($engine->get_session("rechargeprorole") > 3){
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



switch ($rechargepro_subservice){
case "BANK TRANSFER":
$display1  = "<span style='color:black;'>Account Name</span> ";
$display3  = "<span style='color:black;'>Account Number</span>";
$total = $amont+35;
$special = "&#x20A6 35";




        $rowbb = $engine->db_query("SELECT percentage FROM rechargepro_services_agent WHERE services_key = 'FUN' AND rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        if (!empty($rowbb[0]['percentage'])){
            $percentage = $rowbb[0]['percentage'];
            $total = $amont+$percentage;
            $special = "&#x20A6 ".$percentage;
        }



$cardview = "display:none;";
	break;
    
 case "TRANSFER":
$display1  = "<span style='color:black;'>rechargepro Name</span> ";
$display2  = "<span style='color:black;'>rechargepro Number</span>";
$display3  = "";
$cardview = "display:none;";
	break;
}

$semail = "guest@user.com";
if($engine->get_session("rechargeproemail")){
  $semail = $engine->get_session("rechargeproemail");  
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
            custom_logo:"https://rechargepro.com.ng/theme/classic/images/logo.png",
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
 jQuery.fn.calllink("/theme/classic/pages/call/walletpayment.php?width=400");
     }
     

      
    }
    
 

    }
    
    
    });   
    
};
</script>


<div class="sitewidth" style="margin-right:auto; margin-left:auto; overflow: hidden;">







<div class="radious5 mainbg" style="padding: 20px; text-align: center; border: solid 1px #EEEEEE; margin:20px;">
<div style="margin-bottom: 10px;">Refer a friend & get paid for all transactions they do</div>

<a href="/invite"><input type="button" class="greenmenu radious5" style="font-size:130%; border: none; padding:5px; cursor: pointer; margin-right: 10px;" value="Refer Your friend" /></a>
<a href="/support"><input type="button" class="greenmenu radious5" style="font-size:130%; border: none; padding:5px; cursor: pointer;" value="Become a Partner" /></a>

</div>









<style type="text/css">
.btt{font-family: Trebuchet MS, Verdana, Arial, Helvetica, sans-serif; padding:20px 2px 20px 2px; -moz-box-shadow: 0px 1px 0px 0px #E9E9E9; -webkit-box-shadow: 0px 1px 0px 0px #E9E9E9; box-shadow: 0px 1px 0px 0px #E9E9E9; border-bottom: 1px solid #F5F5E5; }
</style>

<div style="font-size: 180%;">Please Confirm your Transaction Details below</div>

<div style="margin:3px; background-color:white; color:black; padding: 20px; float:left; width:65%;">

<div class="btt">
<div style="float: left; width:30%;">Product</div><div style="float:left; 68%"><?php echo $row[0]['rechargepro_service'];?></div> 
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

<div class="nInformation" style="margin: 10px; text-align: left;"> Select Payment Method</div>
<div id="loadingarea"></div>
<div style="overflow: hidden;">
<img src="/theme/classic/images/card.png" onclick="payment_method('1','<?php echo $total;?>')" style="<?php echo $cardview;?> cursor:pointer; width: 50%; float:left;" />
<img src="/theme/classic/images/waller.png" onclick="payment_method('2','<?php echo $total;?>')" style="cursor:pointer; width: 20%; float:right;" />
</div>
</div>



<div style="float: right; width:27%; border-left: solid 1px #CCCCCC; height:400px; padding:20px;">
<div style="color: black; font-size:150%; margin-bottom: 10px;">rechargepro</div>


<div style="color: black;">Buy Electricity</div>
<div style="overflow: hidden; margin-bottom: 15px;">
<form action="/index#utility" method="POST">
<input name="amount" type="text" style="float:left; width: 77%; padding:10px;" placeholder="Amount" class="input" />
<button type="submit" style="float:right; width: 22%; border:none; padding:10px;" class="mainbg shadow" >GO</button>
</form>
</div>

<div style="color: black;">Pay Bill</div>
<div style="margin-bottom: 15px;">
<style type="text/css">
.selectize-input{
  height:25px;

}
.selectize-control{float:left;}
</style>
<form action="/index#bills" method="POST">
<select name="key"   style="float:left; width: 77%; padding:10px;" class="input" >
<option  value="" hidden="hidden">Select Bill</option>
<?php
$row = $engine->db_query("SELECT id,services_key,service_name FROM rechargepro_services WHERE services_category = ?  AND status = '1' ORDER BY id",array(7)); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id'];
    $services_key = $row[$dbc]['services_key'];
    $service_name = $row[$dbc]['service_name'];
    ?>
    <option value="<?php echo $services_key;?>"><?php echo $service_name;?></option>
    <?php
    }
?>

</select>
<button type="submit" style="float:right; width: 22%; border:none; padding:10px;" class="mainbg shadow" >GO</button>
</form>
<div style="clear: both;"></div>
</div>


<div style="color: black;">Buy Airtime</div>
<div style="overflow: hidden; margin-bottom: 15px;">
<form action="/index#airtime" method="POST">
<input name="amount" type="text" style="float:left; width: 77%; padding:10px;" placeholder="Amount" class="input" />
<button type="submit" style="float:right; width: 22%; border:none; padding:10px;" class="mainbg shadow" >GO</button>
</form>
</div>


<div style="color: black;">Pay TV Subscription</div>
<div style="margin-bottom: 15px;">

<form action="/index#tv" method="POST">
<select name="key"  style="float:left; width: 77%; padding:10px;" class="input" >

<option  value="" hidden="hidden">Select Network</option>
<?php
$row = $engine->db_query("SELECT id,services_key,service_name FROM rechargepro_services WHERE services_category = ?  AND status = '1' ORDER BY id",array(5)); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id'];
    $services_key = $row[$dbc]['services_key'];
    $service_name = $row[$dbc]['service_name'];
    ?>
    <option value="<?php echo $services_key;?>"><?php echo $service_name;?></option>
    <?php
    }
?>

</select>
<button type="submit" style="float:right; width: 22%; border:none; padding:10px;" class="mainbg shadow" >GO</button>
</form>
<div style="clear: both;"></div>
</div>



</div>



</div>