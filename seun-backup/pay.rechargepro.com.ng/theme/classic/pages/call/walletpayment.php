<?php
require "../../../../engine.autoloader.php";


if(isset($_REQUEST['cartt'])){
$cart = $_REQUEST['cartt']; 
$channel = "1";

if(!$engine->get_session("quickpayid")){ echo "bad@@Please Login to continue"; exit;}
$quickpayrole = $engine->get_session("quickpayrole");
$quickpayid = $engine->get_session("quickpayid");




$row = $engine->db_query("SELECT public_secret,service_charge,is_service_charge FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($engine->get_session("quickpayid")));	 
$public_secret = $row[0]['public_secret'];
$service_charge = $row[0]['service_charge'];
$is_service_charge = $row[0]['is_service_charge'];
if($quickpayrole < 4){
if($is_service_charge == 1){
//$engine->db_query("UPDATE quickpay_transaction_log SET service_charge = ? WHERE transactionid = ? LIMIT 1",array($service_charge,$cart));	  
}
}





$row = $engine->db_query("SELECT quickpay_subservice FROM quickpay_transaction_log WHERE transactionid = ? LIMIT 1",array($cart));
$quickpay_subservice = $row[0]['quickpay_subservice'];

             
             
             
$row = $engine->db_query("SELECT services_category FROM quickpay_services WHERE services_key = ? LIMIT 1",array($quickpay_subservice));
$services_category = $row[0]['services_category'];
  



if(empty($services_category)){
$services_category = $quickpay_subservice;
}

$link = "";
switch ($services_category){
	case 1:
    $link = "pro/electricity/complete_transaction.json";//buy_power
	break;

	case 2:
    case 3:
    case 4:
    $link = "pro/airtime_data/complete_transaction.json";
	break;

	case 5:
    $link = "pro/tv/complete_transaction.json";//tv
	break;
    
    case 6:
    $link = "pro/lottery/complete_transaction.json";//buy_lottery
	break;
    
    case 7:
    $link = "pro/bills/complete_transaction.json";//pay_bills
	break;
    
    case "BANK TRANSFER":
    $link = "pro/bank_transfer/complete_transaction.json";//pay_transfer
	break;
    
        
    case "TRANSFER":
    $link = "pro/transfer/complete_transaction.json";//pay_transfer
	break;
    
   default :  echo "bad@@Invalid Selection"; exit;
}



$payload = array("tid"=>$cart,"private_key"=>$public_secret,"serial"=>"web","channel"=>$channel);
$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/".$link);



  if($responseData["status"] == "200"){
        
        
        $engine->destroy_session("cartid");
        echo "ok@@".json_encode($responseData['message']); exit;
        
        
    }elseif(is_array($responseData['message'])){
        
        echo "okb@@".json_encode($responseData['message']); exit;
    }else{
          echo "bad@@".$responseData["message"]; exit;    
        
      
    }
   
}
?>




<?php
	if(!$engine->get_session("quickpayid")){
?>
<script type="text/javascript">
function logmein(){
       var username = $("#username").val();
       var password = $("#password").val();
       var returnurl = $("#returnurl").val();
       
       $('#status').html("").hide(); 
       
       if(username == "" || password == ""){
       $('#status').html('Invalid login details').show();
       $('#loading').html(""); return false; }
       
       
       $("#loginbtn").html('<i class="fa fa-spinner fa-spin"></i> Loading');
                                  
                        $.ajax({
                        type: "POST",
                        url: "/secure/login",
                        data: 'username='+username+'&password='+password+"&returnurl="+encodeURIComponent(returnurl),
                        cache: false,
                        success: function(html){
$("#loginbtn").html('LOGIN TO CONTINUE');                
var res = html.split("*");
                  
switch (res[0]){ 
	case "bad":
    $('#status').html("Invalid Login Details").show(); 
	break;

	case "ac":
    window.location.href = "activateaccount";
	break;

	case "block":
    $('#status').html("Your account has been blocked, please contact the administrator").show(); 
	break;

	case "auth":
    $('#status').html("This operation is not authorised, Please enter authorisation code from an authorised device, to authrised web login").show(); 
    $(".loginholder").hide();
    $(".authholder").show();
	break;

    
    case "ok":
       $.lightbox("/theme/classic/pages/call/walletpayment.php", {
        'width'       : 400,
        'height'      : 220,
        'autoresize'  : true
      });
	break;
    
	default :     $('#status').html(html).show(); 
}  
                       
                       return false; 
                        }
                        });
       
       
 
  
    $(".authbutton").click(function (){   

       var auth = $("#auth").val();
       
       $('#status').html("").hide(); 
       
       if(auth == ""){
       $('#status').html('Invalid Code').show();
       $('#loading').html(""); return false; }
       
       
       $("#loadingb").html('<img src="theme/classic/images/camera-loader.gif" width="16" height="16" /> loading...');
                                  
                        $.ajax({
                        type: "POST",
                        url: "secure/auth",
                        data: 'auth='+auth+"&username="+username,
                        cache: false,
                        success: function(html){
$('#loadingb').html("");                 
var res = html.split("*");
                  
switch (res[0]){ 
	case "bad":
    $('#status').html("Invalid Code").show(); 
	break;
        
    case "goo":
    $('#status').html("Maximum device allowed").show(); 
	break;
    
    case "ok":
    $('#status').html("").hide(); 
    $('#statusb').html("Authorised, please login <span clas='fas fa-check-circle' style='color:#0BB20B;'></span>").show();
    $(".loginholder").show();
    $(".authholder").hide();
	break;
    
	default :     $('#status').html(html).show(); 
}  
                       
                       return false; 
                        }
                        });
       
       
     return false;  

	});  
 
}
</script>
<div id="status" class="nWarning" style="margin:5px 3%; display: none;"></div>

<div class="loginholder" style="overflow: hidden;">
<div style="padding:3%;">
<div style="font-size: 20px; margin-bottom:5px;">Sign In</div>
<div style=" margin-bottom:5px;">Signin below to continue</div>
<div>Email Address</div>
<div style="margin-bottom: 5px;"><input id="username" class="input" type="text" style="padding:5px; width: 98%;" /></div>
<div>Password</div>
<div style="margin-bottom: 5px;"><input id="password" class="input" type="password" style="padding:5px; width: 98%;" /></div>
<div style="margin-bottom: 10px;"><a href="forgetpassword">Forget your password?</a> </div>

<input type="hidden" value="" id="returnurl" />

<span id="loading"></span>

<button id="loginbtn" class="mainbg" onclick="logmein()" style="border:none; font-size:110%; color:white; padding:10px 20px; margin-bottom: 10px; width:99%; cursor: pointer;"> LOGIN TO CONTINUE</button>

</div>
</div>



<div class="authholder" style="padding:0px 3%; overflow: hidden; display:none;">
<div style="border: solid 1px #EEEEEE; padding:3%; background-color: white;">
<div style="font-size: 20px; margin-bottom:5px;">Authorisation</div>
<div style=" margin-bottom:5px;">Enter code below to continue</div>
<div>AUTHORISATION CODE</div>
<div style="margin-bottom: 5px;"><input id="auth" class="input" type="text" style="padding:10px 5px; width: 98%;" /></div>

<span id="loadingb"></span>

<button class="mainbg authbutton" style="border:none; font-size:110%; color:white; padding:10px 20px; margin-bottom: 10px; width:99%; cursor: pointer;" type="submit">GET PERMISSION</button>


</div>
</div>

<?php
	}
?>





<?php
$quickpayid = $engine->get_session("quickpayid");
if($engine->get_session("quickpayid")){
$row = $engine->db_query("SELECT ac_ballance,profit_bal FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($engine->get_session("quickpayid")));
$ac_ballance = $row[0]['ac_ballance']; 	   
$profit_bal = $row[0]['profit_bal'];       
       
	   
$row = $engine->db_query("SELECT service_charge,quickpay_service,quickpay_subservice,account_meter,amount,phone,quickpay_status FROM quickpay_transaction_log WHERE transactionid = ? LIMIT 1",array($engine->get_session("cartid")));
$quickpay_service = $row[0]['quickpay_service']; 
$quickpay_subservice = $row[0]['quickpay_subservice']; 
$account_meter = $row[0]['account_meter']; 
$amount = $row[0]['amount']; 
$phone = $row[0]['phone']; 
$quickpay_status = $row[0]['quickpay_status'];
$service_charge = $row[0]['service_charge'];
?>



<script type="text/javascript">
function pay_now(Id){
    if(Id == "1"){
$("#sendutility").html('<i class="fa fa-spinner fa-spin"></i> Loading'); }else{   
$("#sendutilityb").html('<i class="fa fa-spinner fa-spin"></i> Loading');  }
    
$.ajax({
type: "POST",
url: "/theme/classic/pages/call/walletpayment.php",
data: 'cartt=<?php echo$engine->get_session("cartid");?>&channel='+Id,
cache: false,
success: function(html){
//console.log(html);

var myreturn = html.split('@@');
        if(myreturn[0].trim() == "ok"){
        
        var jsonObj = myreturn[1];
        var obj = $.parseJSON(jsonObj);
        
        //obj["TransactionID"];
        //obj["status"];
        $.alert("Transaction Successful");
        window.location.href= "/invoice&id=<?php echo $quickpayid;?>_"+obj["TransactionID"];
        }else if(myreturn[0].trim() == "okb"){
        var jsonObj = myreturn[1];
        var obj = $.parseJSON(jsonObj);
        window.location.href= "/invoice&id=<?php echo $quickpayid;?>_"+obj["TransactionID"];    
        }else{
        $.alert(myreturn[1]);
        };
    
       
  $("#sendutility").html('PAY NOW &#x20A6 <?php echo $ac_ballance;?>'); 
}
});
}
</script>

<div style="padding: 20px; overflow: hidden;">
<div style="text-align: right;margin-top:-20px; overflow:hidden;">
<img src="/theme/classic/images/logo2.png" width="20%" style="float: left;" />
<div style="float: right; width:80%;">Bal: &#x20A6 <?php echo $ac_ballance;?></div>
</div>

<div style="font-size: 140%; margin-bottom:10px;"><?php echo $quickpay_service;?> :: <?php echo $quickpay_subservice;?></div>
<div style="overflow: hidden;"></div>

<div style="margin-bottom: 5px;">Account : <?php echo $account_meter;?></div>
<div style="margin-bottom: 5px;">Phone : <?php echo $phone;?></div>
<div style="margin-bottom: 5px;">Amount : &#x20A6 <?php echo $amount+$service_charge;?></div>

<button onclick="pay_now('1')" id="sendutility" class="mainbg radious10" style="cursor:pointer; border:none; height: 30px; padding:8px 1%; margin:0px; width:100%; margin-bottom:5px;">PAY NOW &#x20A6 <?php echo $ac_ballance;?></button>


</div>

<?php
	}
?>






