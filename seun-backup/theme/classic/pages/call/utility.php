<?php
require "../../../../engine.autoloader.php";

if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        echo "<div style='color:white; font-size:150%; text-align:center; margin-top:50px;'>this Account is not authorised for this action</div>"; exit;
    }
}

if(isset($_REQUEST['tel'])){
    
$array = array();
$row = $engine->db_query("SELECT services_key FROM rechargepro_services WHERE services_category = ?  AND status = '1'",array(1)); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $services_key = $row[$dbc]['services_key'];

$array[] = $services_key;
    }
    
    $implode = "'".implode("','",$array)."'";
    
   $row = $engine->db_query("SELECT transactionid,rechargeproid FROM rechargepro_transaction_log WHERE rechargepro_subservice IN ($implode)  AND rechargepro_status = 'PAID' ORDER BY transactionid DESC LIMIT 1",array());  
    $transactionid = $row[0]['transactionid'];
    
    if(empty($transactionid)){ echo "bad"; exit;}
    echo  $row[0]['rechargeproid']."_".$transactionid;exit;
    }
    
    
    
    
if(isset($_REQUEST['preview'])){


    if(!isset($_REQUEST['utility']) || !isset($_REQUEST['meter']) || !isset($_REQUEST['amount']) || !isset($_REQUEST['phone'])){
        echo "bad@@Please enter the compulsory fields"; exit;
    }
    
    
        
        
    $utility = urldecode($_REQUEST['utility']); 
    $meter = urldecode($_REQUEST['meter']); 
    $amount = urldecode($_REQUEST['amount']); 
    $phone = urldecode($_REQUEST['phone']); 
    $email = urldecode($_REQUEST['email']); 



$payload = array("accountnumber"=>$meter,"service"=>$utility,"amount"=>$amount,"mobile"=>$phone,"email"=>$email);
if($engine->get_session("rechargeproid")){
$row = $engine->db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($engine->get_session("rechargeproid")));  
if(!empty($row[0]['public_secret'])){
    $payload["private_key"] = $row[0]['public_secret'];
}
}else{
    $payload["private_key"] = "web";
}
$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/pro/electricity/auth_transaction.json");



  if($responseData["status"] == "200"){
    $engine->put_session("cartid",$responseData['message']["tid"]);
        echo "ok@@".json_encode($responseData['message']); exit;
    }else{
      echo "bad@@".$responseData["message"]; exit;  
    }

}


?>
<?php
if(isset($_REQUEST['key'])){
    $key = trim($engine->safe_html($_REQUEST['key']));
    if(!empty($key)){
    ?>
<script type="text/javascript">
jQuery(document).ready(function($){
if ( $("#utility option[value='<?php  echo $engine->safe_html($_REQUEST['key']);?>']").val() !== undefined) { 
$("#utility").val("<?php  echo $engine->safe_html($_REQUEST['key']);?>");
}
});
</script>
<?php }}?>



<script type="text/javascript">
var amount = 0;
function sendservice(){
var utility = $("#utility").val();
var utilitytext = $("#utility option:selected").text();
var meter = $("#meter").val();
amount = $("#amount").val();
var phone = $("#phone").val();
var email = $("#email").val();


if(empty(utility) || empty(meter) || empty(amount) || empty(phone)){
  $.alert("All fields are compulsory"); return false; 
}


   if(phone.length < 11 || phone.length > 11){
      $.alert("Invalid Phone Number (11 Digits Required"); return false; 
    }
    
    
    
$("#sendutility").html('<i class="fa fa-spinner fa-spin"></i> Loading');    
    
    var datatosend = "utility="+encodeURIComponent(utility)+"&meter="+encodeURIComponent(meter)+"&amount="+encodeURIComponent(amount)+"&phone="+encodeURIComponent(phone)+"&email="+encodeURIComponent(email)+"&preview=preview";
    
$.ajax({
type: "POST",
url: "theme/classic/pages/call/utility.php",
data: datatosend,
cache: false,
success: function(html){
    
    
var myreturn = html.split('@@');   

    
if(myreturn[0].trim() == "ok"){
    //thirdPartyCode
var jsonObj = myreturn[1];
var obj = $.parseJSON(jsonObj);
    
    var res1 = obj["name"].replace("/", "_");
    var res2 = obj["address"].replace("/", "_");
    window.location.href = "/confirmation&detail1="+encodeURIComponent(res1)+"&detail2="+encodeURIComponent(res2);


}else{
$.alert(myreturn[1]);
//jQuery.fn.calllink("theme/classic/pages/call/preview.php?width=500");
}
  
  
   $("#sendutility").html('Continue');   
},error: function(xhr, status, error) {

       $.alert(status);

    }
});
    

//amount = parseInt(amount);


}

function recover(){
    $("#recover").html('<i class="fa fa-spinner fa-spin"></i> Loading');
    var tel = $("#tel").val();
    
    $.ajax({
    type: "POST",
    url: "theme/classic/pages/call/utility.php",
    data: "tel="+tel,
    cache: false,
    success: function (dat){
        $("#recover").html('Recover');
        if(dat.trim() == "bad"){
            $.alert("No record found");
            return false;
        }
        window.location.href= "invoice&id="+dat;   
        }
        })
}
</script>



<div id="main1" style="padding: 20px;" class="profilebg">


<div style="text-align: right; margin-top:-10px;"><a href="/home"><img src="/java/display/images/close2.png" width="24" height="24" /></a></div>

<div style="font-size: 140%; margin-bottom:10px;">ELECTRICITY VENDING</div>
<div style="overflow: hidden;"></div>







<style type="text/css">
.inputholder{float:left; width:48%;}
.inputholder2{margin-right:4%; }
@media only screen and (max-width: 525px) {
    /* For mobile phones: */
.inputholder{float:none; width:100%;}
.inputholder2{margin-right:0%; }
}
</style>



<div style="overflow: hidden;">

<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<select id="utility" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;" >
<option  value="" hidden="hidden">Select Utility</option>
<?php
$row = $engine->db_query("SELECT id,services_key,service_name FROM rechargepro_services WHERE services_category = ?  AND status = '1' ORDER BY id",array(1)); 
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
</div>
  
  
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input id="meter" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;"  placeholder="Enter Meter Number"/>
</div>

</div>





<div style="overflow: hidden;">
<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<input id="amount" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;" type="number" placeholder="Enter Amount"/>
</div>
  
  
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input id="phone" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;" type="tel" placeholder="Enter Phone Number"/>
</div>

</div>





<div style="overflow: hidden;">

<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input id="email" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;" type="email" placeholder="Email Address not compulsory"/>
</div>
  
</div>




<div style="margin-bottom:10px;">Token will be sent to the email/phone number entered above</div>


<div style="overflow: hidden;">

<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<button onclick="sendservice()" id="sendutility" class="mainbg" style=" color:white; cursor:pointer; border:none; padding:20px 1%; margin:0px; float:left; width:100%;">PAY</button>
</div>
  
</div>

<div style="cursor:pointer; color: maroon;" onclick="$('#main1').hide(); $('#main2').show();">Recover your lost token?</div>

</div>



<!--  -->

<div id="main2" style="padding: 20px; display:none;" class="profilebg">

<div style="font-size: 140%; margin-bottom:10px;">ELECTRICITY VENDING</div>
<div style="overflow: hidden;"></div>
<div style="margin-bottom:10px;">Recover your lost token</div>

<input id="tel" class="input" style="padding:20px 1%; margin:0px; margin-bottom:10px; width:100%;" type="tel" placeholder="Enter Phone Number"/>

<button class="mainbg" id="recover" onclick="recover()" style="cursor:pointer; border:none; padding:20px 1%; margin:0px; float:left; width:100%; margin-bottom:10px; color:white;">Recover</button>

<div style="cursor:pointer; margin-bottom:10px; color: orange;" onclick="$('#main1').show(); $('#main2').hide();">&laquo; Back</div>
</div>