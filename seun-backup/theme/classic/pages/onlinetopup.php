<?php
include "../../../../engine.autoloader.php";

if(isset($_REQUEST['wallet'])){
    $amount = $_REQUEST['amount'];
    
    
    $payload = array("amount"=>$amount);
    if($engine->get_session("rechargeproid")){
    $row = $engine->db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($engine->get_session("rechargeproid")));  
    if(!empty($row[0]['public_secret'])){
        $payload["private_key"] = $row[0]['public_secret'];
    }
    }

    $responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/local/transfer/auth_topup.json");
    
    
  if($responseData["status"] == "200"){
    $engine->put_session("cartid",$responseData['message']["tid"]);
        echo "ok@@".json_encode($responseData['message']); exit;
    }else{
      echo "bad@@".$responseData["message"]; exit;  
    }
}



$semail = "guest@user.com";
if($engine->get_session("rechargeproemail")){
  $semail = $engine->get_session("rechargeproemail");  
}
?>

<script type="text/javascript" src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function($){
$('.tunnel').tunnel();
});




function online(){
    var amountopay = $("#transferamount").val();
    
        if(empty(amountopay)){
            $.alert("Invalid Amount"); return false;
        }
  
    
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/topup/onlinetopup.php",
		data: "wallet=wallet&amount="+amountopay,
		cache: false,
		success: function(html) {
        
        var myreturn = html.split('@@');   
            
            
        if(myreturn[0].trim() == "ok"){
            //thirdPartyCode
            var jsonObj = myreturn[1];
            var obj = $.parseJSON(jsonObj);
            
                    
        var pref = Math.random()+"_"+obj["tid"];
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
            window.location.href="/pay/topuppayment.php?flw_ref="+flw_ref+"&id="+obj["tid"]; 
        	}
        });
                    
          // jQuery.fn.calllink("/theme/classic/pages/profile/transferpreview.php?width=500&detail1="+encodeURIComponent(obj["name"])+"&detail2="+encodeURIComponent(obj["details"]));
                    
        
          }else{
            $.alert(myreturn[1]);
          }
		}
	});
}
</script>

<div class="profilebg" style="padding: 15px; margin-top:-20px;">


<div style="overflow: hidden; text-align: left;" class="nInformation">
For instant crediting via Bank transfer/deposit, please use the details below<br />
<strong>Bank Name </strong>: FIRST BANK<br />
<strong>Account Name</strong>: Vertis Technologies Ltd<br />
<strong>Account Number</strong>:3132981212<br />
<strong>Description</strong>: &lt;Your RechargePro Phone Number&gt;
</div>
<?php
    
    	if($engine->get_session("rechargeproid")){
?>



<div style="font-size:130%; color:black;">Online Topup</div>

<div>
<div>Enter Amount</div>
<input type="text" id="transferamount" style="width: 99.9%; padding:5px 0px; margin-bottom: 5px;" class="input" />
<div>Online Topup Attracts Bank process fee of 1.5%</div>
<button onclick="online()" style="cursor:pointer; width: 99.9%; border:none; padding:4px;" class="mainbg shadow">TOPUP NOW</button>
</div>

<?php
	}
?>






















<?php
	if(!$engine->get_session("rechargeproid")){
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
     jQuery.fn.calllink("/theme/classic/pages/topup/onlinetopup.php?width=500");
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
	}?>
</div>
