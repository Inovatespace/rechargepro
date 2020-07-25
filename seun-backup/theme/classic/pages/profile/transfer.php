<?php
include "../../../../engine.autoloader.php";



if(isset($_REQUEST['btaccount'])){
    $btaccount = $_REQUEST['btaccount'];
    $btcode = $_REQUEST['btcode'];
    $btamount = $_REQUEST['btamount'];
    $narration = $_REQUEST['narration'];

    
    $payload = array("amount"=>$btamount,"account"=>$btaccount,"narration"=>$narration,"bankcode"=>$btcode);
    if($engine->get_session("rechargeproid")){
    $row = $engine->db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($engine->get_session("rechargeproid")));  
    if(!empty($row[0]['public_secret'])){
        $payload["private_key"] = $row[0]['public_secret'];
    }
    }

    $responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/v1/bank_transfer/auth_transfer.json");
    
    
  if($responseData["status"] == "200"){
    $engine->put_session("cartid",$responseData['message']["tid"]);
        echo "ok@@".json_encode($responseData['message']); exit;
    }else{
      echo "bad@@".$responseData["message"]; exit;  
    }
}

if(isset($_REQUEST['wallet'])){
    $email = $_REQUEST['email'];
    $amount = $_REQUEST['amount'];
    
    
    $payload = array("amount"=>$amount,"account"=>$email);
    if($engine->get_session("rechargeproid")){
    $row = $engine->db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($engine->get_session("rechargeproid")));  
    if(!empty($row[0]['public_secret'])){
        $payload["private_key"] = $row[0]['public_secret'];
    }
    }

    $responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/v1/transfer/auth_transfer.json");
    
    
  if($responseData["status"] == "200"){
    $engine->put_session("cartid",$responseData['message']["tid"]);
        echo "ok@@".json_encode($responseData['message']); exit;
    }else{
      echo "bad@@".$responseData["message"]; exit;  
    }
}
?>
<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function($){
$('.tunnel').tunnel();
});
</script>

<style type="text/css">
.myarrow {
    color:white;
	position: relative;
	background: #398a2c;
	border: 2px solid #c2e1f5;
    padding:3px;
    margin:3px;
}
.myarrow:after, .myarrow:before {
	left: 100%;
	top: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
}

.myarrow:after {
	border-color: rgba(57, 138, 44, 0);
	border-left-color: #398a2c;
	border-width: 10px;
	margin-top: -10px;
}
.myarrow:before {
	border-color: rgba(194, 225, 245, 0);
	border-left-color: #c2e1f5;
	border-width: 13px;
	margin-top: -13px;
}
</style>
<div class="profilebg" style="padding: 15px;  margin-top:-20px;">

<script type="text/javascript">
function transfer(Id){
    $(".tfar").hide();
    $("#"+Id).show();
}


function wallettransfer(){
    var email = $("#transferemail").val();
    var amount = $("#transferamount").val();
    
        if(empty(amount)){
            $.alert("Invalid Amount"); return false;
        }
        
       if(empty(email)){
            $.alert("All Fields are compulsory"); return false;
        }
        
        
        $("#wt").html('<i class="fa fa-spinner fa-spin"></i> Loading');
    
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/profile/transfer.php",
		data: "email="+email+"&wallet=wallet&amount="+amount,
		cache: false,
		success: function(html) {
        
        var myreturn = html.split('@@');   
            
            
        if(myreturn[0].trim() == "ok"){
            //thirdPartyCode
            var jsonObj = myreturn[1];
            var obj = $.parseJSON(jsonObj);
                
                
                              
    var res1 = obj["name"].replace("/", "_");
    var res2 = obj["ac"].replace("/", "_");
    window.location.href = "/confirmation&detail1="+encodeURIComponent(res1)+"&detail2="+encodeURIComponent(res2);
      
          
    //jQuery.fn.calllink("/theme/classic/pages/profile/transferpreview.php?width=500&detail1="+encodeURIComponent(obj["name"])+"&detail2="+encodeURIComponent(obj["details"]));
                    
        
          }else{
            $("#wt").html('Proceed');
            $.alert(myreturn[1]);
          }
		}
	});
}




function wallet_bank(){
    
var btcode = $("#btcode").val();
var btaccount = $("#btaccount").val();
var btamount = $("#btamount").val();
var narration = $("#narration").val();
        
       if(empty(btcode) || empty(btaccount) || empty(btamount) || empty(narration)){
            $.alert("All Fields are compulsory"); return false;
        }
        
        
        $("#wb").html('<i class="fa fa-spinner fa-spin"></i> Loading');
    
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/profile/transfer.php",
		data: "btcode="+btcode+"&btaccount="+btaccount+"&btamount="+btamount+"&narration="+narration,
		cache: false,
		success: function(html) {
        
        var myreturn = html.split('@@');
            
        if(myreturn[0].trim() == "ok"){
            //thirdPartyCode
            var jsonObj = myreturn[1];
            var obj = $.parseJSON(jsonObj);
              
    var res1 = obj["name"].replace("/", "_");
    var res2 = obj["ac"].replace("/", "_");
    window.location.href = "/confirmation&detail1="+encodeURIComponent(res1)+"&detail2="+encodeURIComponent(res2);
          
        
          }else{
            $("#wb").html('Verify Account');
            $.alert(myreturn[1]);
          }
		}
	});
}
</script>

<div style="font-size:130%;color:black;">Fund Transfer</div>
<div style="text-align: left; font-size:90%" class="nInformation">Transfer fund between rechargepro Account and To all banks</div>

<div style="overflow: hidden; font-size:85%;">
<div style="cursor: pointer;" onclick="transfer('wallet')" class="myarrow">Transfer to rechargepro Wallet</div>
<div style="display: none; padding:5px; border: solid 1px #CCCCCC;" id="wallet" class="tfar">
<div style="color: #003451;">Transfer to rechargepro Wallet</div>
<div>Enter rechargepro mobile or Email</div>
<div><input type="text" class="input" id="transferemail" style="width: 99.9%;" /></div>
<div>Enter Amount</div>
<div><input type="text" class="input" id="transferamount" style="width: 99.9%;" /></div>
<button onclick="wallettransfer()" id="wt" class="activemenu" style="cursor:pointer; padding:5px; margin-top:5px; border:none; width: 99.9%;" >Proceed</button>
</div>


<div style="cursor: pointer;" onclick="transfer('agent')"  class="myarrow">Withdraw from rechargepro Agent</div>
<div style="display: none;" id="agent" class="tfar nWarning">Coming Soon</div>
<div style="cursor: pointer;" onclick="transfer('bank')"  class="myarrow">Transfer to Bank Account</div>





<div style="display: none; padding:5px; border: solid 1px #CCCCCC;" id="bank" class="tfar profilebg">
<div>Select Bank</div>
<select id="btcode" style="width: 100%; margin-bottom:5px;" class="input">
	<?php
    $row = $engine->db_query("SELECT setting_value FROM settings WHERE setting_key = 'bank_code'",array());
    $bankcodes = $row[0]['setting_value'];
    
    $bankcodes = json_decode($bankcodes,true);
    ksort($bankcodes);
foreach($bankcodes AS $key => $val){
?>
<option value="<?php echo $key;?>"><?php echo $val;?></option>
<?php
	}
?>
</select>

<div style="overflow: hidden;">
<div style="float: left; width:48%;">
<div>Enter Bank Account Number</div>
<input type="text"  id="btaccount" style="width: 100%; margin-bottom:5px;" class="input"/>
</div>

<div style="float: right; width:48%;">
<div>Enter Amount</div>
<input type="text" id="btamount" style="width: 100%; margin-bottom:5px;" class="input"/>
</div>
</div>

<div>Description</div>
<input type="text" id="narration" style="width: 100%; margin-bottom:5px;" class="input"/>

<button onclick="wallet_bank()" id="wb" class="activemenu" style="cursor:pointer; padding:5px; margin-top:5px; border:none; width: 99.9%;" >Verify Account</button>
</div>


</div>




</div>
