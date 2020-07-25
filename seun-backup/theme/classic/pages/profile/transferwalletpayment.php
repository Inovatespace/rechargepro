<?php
require "../../../../engine.autoloader.php";


if(isset($_REQUEST['cartt'])){
$cart = $_REQUEST['cartt']; 

if(!$engine->get_session("rechargeproid")){ echo "bad@@Please Login to continue"; exit;}



$row = $engine->db_query("SELECT ac_ballance,profile_creator,public_secret FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($engine->get_session("rechargeproid")));
$ac_ballance = $row[0]['ac_ballance']; 	 
$profile_creator = $row[0]['profile_creator'];
$private_key = $row[0]['public_secret'];

$row = $engine->db_query("SELECT amount, rechargepro_subservice FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($cart));
$amount = $row[0]['amount'];
$rechargepro_subservice = $row[0]['rechargepro_subservice'];


if($amount > $ac_ballance){
   echo "bad@@Insufficient Balance"; exit;   
}

//$newballance = $ac_ballance-$amount;
//$engine->db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($newballance,$engine->get_session("rechargeproid")));




//$engine->db_query("UPDATE rechargepro_transaction_log SET rechargepro_status = ?,agent_id=?,rechargeproid=?,payment_method=? WHERE transactionid = ? LIMIT 1",array("PAID",$profile_creator,$engine->get_session("rechargeproid"),2,$cart));




 $engine->destroy_session("cartid");


$payload = array("tid"=>$cart,"private_key"=>$private_key);
$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/v1/transfer/pay_transfer.json");

function fixarray($responseData){
    
    $me = "";
      if(is_array($responseData)){
        
           foreach($responseData AS $key => $value){
            if(is_array($key)){
             $me .= fixarray($key);
            }else{
               if(is_array($key)){
                 $me .= fixarray($value);
                }else{
             $me .= $key.":".$value.",";  
             } 
            }
            
           } 
            
        }else{
            $me .= $responseData;
            }
            return $me;
}

  if($responseData["status"] == "200"){
       
        echo "ok@@".json_encode($responseData['message']); exit;
        
        
    }elseif(is_array($responseData['message'])){
        
        echo "okb@@".json_encode($responseData['message']); exit;
    }else{
        
          echo "bad@@".$responseData["message"]; exit;    
        
      
    }
   
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

	case "block":
    $('#status').html("Your account has been blocked, please contact the administrator").show(); 
	break;
    
    case "ok":
     jQuery.fn.calllink("/theme/classic/pages/profile/transferwalletpayment.php?width=400");
	break;
    
	default :     $('#status').html(html).show(); 
}  
                       
                       return false; 
                        }
                        });
       
       
 

 
}
</script>
<div id="status" class="nWarning" style="display: none;"></div>

<div style="overflow: hidden;">
<div style="border: solid 1px #EEEEEE; padding:3%;">
<div style="font-size: 20px; margin-bottom:5px;">Sign In</div>
<div style=" margin-bottom:5px;">Signin below to continue</div>
<div>Email Address</div>
<div style="margin-bottom: 5px;"><input id="username" class="input" type="text" style="padding:5px; width: 98%;" /></div>
<div>Password</div>
<div style="margin-bottom: 5px;"><input id="password" class="input" type="password" style="padding:5px; width: 98%;" /></div>
<div style="margin-bottom: 10px;"><a href="forgetpassword">Forget your password?</a> </div>

<input type="hidden" value="" id="returnurl" />

<span id="loading"></span>

<button id="loginbtn" class="activemenu" onclick="logmein()" style="border:none; font-size:110%; color:white; padding:10px 20px; margin-bottom: 10px; width:99%; cursor: pointer;"> LOGIN TO CONTINUE</button>

</div>
</div>

<?php
	}
?>





<?php
	if($engine->get_session("rechargeproid")){
$row = $engine->db_query("SELECT ac_ballance FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($engine->get_session("rechargeproid")));
$ac_ballance = $row[0]['ac_ballance']; 	   
       
       
	   
$row = $engine->db_query("SELECT rechargepro_service,rechargepro_subservice,account_meter,amount,phone,rechargepro_status FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($engine->get_session("cartid")));
$rechargepro_service = $row[0]['rechargepro_service']; 
$rechargepro_subservice = $row[0]['rechargepro_subservice']; 
$account_meter = $row[0]['account_meter']; 
$amount = $row[0]['amount']; 
$phone = $row[0]['phone']; 
$rechargepro_status = $row[0]['rechargepro_status'];
?>



<script type="text/javascript">
function pay_now(){
    
$("#sendutility").html('<i class="fa fa-spinner fa-spin"></i> Loading');    
    
$.ajax({
type: "POST",
url: "/theme/classic/pages/profile/transferwalletpayment.php",
data: 'cartt=<?php echo$engine->get_session("cartid");?>',
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
        window.location.href= "/thankyou&id="+obj["TransactionID"];
        }else if(myreturn[0].trim() == "okb"){
        var jsonObj = myreturn[1];
        var obj = $.parseJSON(jsonObj);
        window.location.href= "/thankyou&id="+obj["TransactionID"];    
        }else{
        $.alert(myreturn[1]);
        };
    
    
  $("#sendutility").html('PAY NOW');  
}
});
}
</script>

<div style="padding: 20px; overflow: hidden;">
<div style="text-align: right;margin-top:-20px; overflow:hidden;">
<img src="/theme/rechargeproplaymain/images/logo.png" width="20%" style="float: left;" />
<div style="float: right; width:80%;">AC Ballance: &#x20A6 <?php echo $ac_ballance;?></div>
</div>

<div style="font-size: 140%; margin-bottom:10px;"><?php echo $rechargepro_service;?> :: <?php echo $rechargepro_subservice;?></div>
<div style="overflow: hidden;"></div>

<div style="margin-bottom: 5px;">Account : <?php echo $account_meter;?></div>
<div style="margin-bottom: 5px;">Phone : <?php echo $phone;?></div>
<div style="margin-bottom: 5px;">Amount : &#x20A6 <?php echo $amount;?></div>

<button onclick="pay_now()" id="sendutility" class="activemenu shadow" style="cursor:pointer; border:none; height: 30px; padding:8px 1%; margin:0px; width:100%; margin-bottom:5px;">PAY NOW</button>
</div>

<?php
	}
?>






