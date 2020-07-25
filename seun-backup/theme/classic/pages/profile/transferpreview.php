<?php
require "../../../../engine.autoloader.php";


$id = $engine->get_session("cartid");
$row = $engine->db_query("SELECT rechargepro_subservice,rechargepro_service,account_meter,amount FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($id)); 

$rowb = $engine->db_query("SELECT services_category FROM rechargepro_services WHERE services_key = ? LIMIT 1",array($row[0]['rechargepro_subservice']));


$special = ""; 
$plusamount = 0;



?>
<script type="text/javascript" src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
<script type="text/javascript">

function payment_method(methodofpayment,amount){
    
$("#loadingarea").html('<i class="fa fa-spinner fa-spin"></i> Loading');

var amountopay = parseInt(amount)+parseInt("<?php echo $plusamount;?>");
var datatosend = "paymentmethod="+encodeURIComponent(methodofpayment);




$.ajax({
    type: "POST",
    url: "/theme/classic/pages/service/utility.php",
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
        
        var pref = Math.random()+"_"+arr[1];
       //call web pay
       //say thank you + online receipt
         getpaidSetup({
        	customer_email:"user@example.com",
        	amount:amountopay,
            custom_logo:"https://rechargepro.com.ng/theme/classic/images/logo.png",
        	txref:pref,
        	PBFPubKey:"<?php echo $engine->config('rave_public_key');?>",
        	meta:[{ flightid:3849 }],
        	onclose:function(){},
        	callback:function(response){
                       flw_ref = response.tx.flwRef;//chargeResponse = response.tx.chargeResponseCode;
            window.location.href="/pay/topuppayment.php?flw_ref="+flw_ref+"&id="+arr[1]; 
        	}
        });
     }
        
     if(methodofpayment == 2){
    //if not login tell login
    //if login -> show pay
    //say thank you + online receipt
 jQuery.fn.calllink("/theme/classic/pages/profile/transferwalletpayment.php?width=400");
     }
     

      
    }
    
 

    }
    
    
    });   
    
};
</script>


<?php
?>
<div id="main3" style="padding: 20px;">
<div style="font-size: 120%; margin-top:-40px; margin-bottom:10px;"><?php echo $row[0]['rechargepro_service'];?></div>
<div style="overflow: hidden;"></div>

<div style="margin-bottom: 10px; font-size:140%; color:#0C5D06;" id="info"><?php echo $_REQUEST['detail1']." ".$_REQUEST['detail2'];?></div>
<div style="margin-bottom: 10px;">PHONE/ACCOUNT : <span id="meterc"><?php echo $row[0]['account_meter'];?></span></div>
<div style="font-weight: bold;">TOTAL: &#x20A6<span id="amountc"><?php echo $row[0]['amount'];?></span> <?php echo $special;?></div>

<div class="nInformation" style="margin: 10px;"> Select Payment Method</div>
<div id="loadingarea"></div>
<div style="overflow: hidden;">
<img src="theme/rechargeproplaymain/images/card.png" onclick="payment_method('1','<?php echo $row[0]['amount'];?>')" style="cursor:pointer; width: 80%; float:left;" />
<img src="theme/rechargeproplaymain/images/waller.png" onclick="payment_method('2','<?php echo $row[0]['amount'];?>')" style="cursor:pointer; width: 20%; float:left;" />
</div>
</div>