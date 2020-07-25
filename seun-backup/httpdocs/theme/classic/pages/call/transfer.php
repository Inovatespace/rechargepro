<?php
 ini_set('default_charset', 'windows-1252');
require "../../../../engine.autoloader.php";
$language = $engine->getlanguage();

$country = $engine->country_iso();
$country = $country[0];

if (isset($_REQUEST['btaccount'])) {
    $btaccount = $_REQUEST['btaccount'];
    $btcode = $_REQUEST['btcode'];
    $btamount = $_REQUEST['btamount'];
    $narration = $_REQUEST['narration'];


    $payload = array(
    "lang"=>$engine->current_language(),
    "currency"=>$engine->currency(),
        "amount" => $btamount,
        "account" => $btaccount,
        "narration" => $narration,
        "bankcode" => $btcode);
    if ($engine->get_session("recharge4id")) {
        $row = $engine->db_query("SELECT public_secret FROM recharge4_account WHERE recharge4id = ? LIMIT 1",
            array($engine->get_session("recharge4id")));
        if (!empty($row[0]['public_secret'])) {
            $payload["private_key"] = $row[0]['public_secret'];
        }
    }

    $responseData = $engine->file_get_b($payload, $engine->config("website_root") .
        "api/pro/bank_transfer/auth_transfer.json");


    if ($responseData["status"] == "200") {
        $engine->put_session("cartid", $responseData['message']["tid"]);
        echo "ok@@" . json_encode($responseData['message']);
        exit;
    } else {
        echo "bad@@" . $responseData["message"];
        exit;
    }
}

if (isset($_REQUEST['wallet'])) {
    $email = $_REQUEST['email'];
    $amount = $_REQUEST['amount'];


    $payload = array("amount" => $amount, "account" => $email);
    if ($engine->get_session("recharge4id")) {
        $row = $engine->db_query("SELECT public_secret FROM recharge4_account WHERE recharge4id = ? LIMIT 1",
            array($engine->get_session("recharge4id")));
        if (!empty($row[0]['public_secret'])) {
            $payload["private_key"] = $row[0]['public_secret'];
        }
    }

    $responseData = $engine->file_get_b($payload, $engine->config("website_root") .
        "api/pro/transfer/auth_transfer.json");


    if ($responseData["status"] == "200") {
        $engine->put_session("cartid", $responseData['message']["tid"]);
        echo "ok@@" . json_encode($responseData['message']);
        exit;
    } else {
        echo "bad@@" . $responseData["message"];
        exit;
    }
}
?>

<script type="text/javascript">
jQuery(document).ready(function($){
$(':input').on('focus', function () {
  $(this).attr('autocomplete', 'off')
});
});
</script>
<div id="main1" style="padding: 20px;" class="menubody">



<script type="text/javascript">
function transfer(){
var Id = $("#choice").val();
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
            $.alert("<?php echo $language["{%ALL_FAAC%}"]; ?>"); return false;
        }
        
        
        $("#wt").html('<i class="fa fa-spinner fa-spin"></i> <?php echo $language["{%LOADING%}"]; ?>');
    
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/call/transfer.php",
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
            $.alert("<?php echo $language["{%ALL_FAAC%}"]; ?>"); return false;
        }
        
        
        $("#wb").html('<i class="fa fa-spinner fa-spin"></i> <?php echo $language["{%LOADING%}"]; ?>');
    
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/call/transfer.php",
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
            $("#wb").html('<?php echo $language["{%VERIFY_ACCOUNT%}"]; ?>');
            $.alert(myreturn[1]);
          }
		}
	});
}
</script>







<div style="overflow: hidden;">

<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<select id="btcode" style="padding:20px 1%; padding-left:50px; margin:0px; float:left; width:100%;" class="input">
	<?php
$row = $engine->db_query("SELECT setting_value FROM settings WHERE setting_key = 'bank_code'",
    array());
$bankcodes = $row[0]['setting_value'];

$bankcodes = json_decode($bankcodes, true);
asort($bankcodes);
foreach ($bankcodes as $key => $val) {
?>
<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
<?php
}
?>
</select><span class="focus-border"><i></i></span>
</div>
  
  
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input id="btaccount" class="input" style="padding:20px 1%; padding-left:50px; margin:0px; float:left; width:100%;"  placeholder="<?php echo
$language["{%EBAN%}"]; ?>"/><span class="focus-border"><i></i></span>
</div>

</div>


<div style="overflow: hidden;">
<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<input id="btamount" class="input" style="padding:20px 1%; padding-left:50px; margin:0px; float:left; width:100%;" type="number" placeholder="<?php echo
$language["{%ENTER_AMOUNT%}"]; ?>"/><span class="focus-border"><i></i></span>
</div>
  
  
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input id="narration" class="input" style="padding:20px 1%; padding-left:50px; margin:0px; float:left; width:100%;" type="tel" placeholder="<?php echo
$language["{%DESCRIPTION%}"]; ?>"/><span class="focus-border"><i></i></span>
</div>

</div>





<div style="overflow: hidden;">
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<div class="container-contact100-form-btn">
<div class="wrap-contact100-form-btn">
<div class="contact100-form-bgbtn"></div>
<button class="contact100-form-btn" onclick="wallet_bank()" id="wb">
<span> <?php echo $language["{%VERIFY_ACCOUNT%}"]; ?> <i class="fa fa-long-arrow-right m-l-7" aria-hidden="true"></i>
</span>
</button>
</div>
</div>
</div>

  
</div>

</div>
