<?php
require "../../../../engine.autoloader.php";

if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        echo "<div style='color:white; font-size:150%; text-align:center; margin-top:50px;'>this Account is not authorised for this action</div>"; exit;
    }
}


if(isset($_REQUEST['preview'])){


    if(!isset($_REQUEST['cabletv']) || !isset($_REQUEST['meter']) || !isset($_REQUEST['code']) || !isset($_REQUEST['amount']) || !isset($_REQUEST['phone'])){
        echo "bad@@Please enter the compulsory fields"; exit;
    }
    
    
    $cabletv = urldecode($_REQUEST['cabletv']); 
    $meter = urldecode($_REQUEST['meter']); 
    $amount = urldecode($_REQUEST['amount']); 
    $code = urldecode($_REQUEST['code']);
    $phone = urldecode($_REQUEST['phone']); 
     
    
    
    $tmpamt = explode(" ",$amount);
    if(!isset($tmpamt[1])){
      echo "bad@@Invalid Amount"; exit;   
    }
    

$payload = array("accountnumber"=>$meter,"service"=>$cabletv,"amount"=>trim($tmpamt[0]),"mobile"=>$phone,"code"=>$code);

if($engine->get_session("quickpayid")){
$row = $engine->db_query("SELECT public_secret FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($engine->get_session("quickpayid")));  
if(!empty($row[0]['public_secret'])){
    $payload["private_key"] = $row[0]['public_secret'];
}
}else{
    $payload["private_key"] = "web";
}

$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/pro/tv/auth_transaction.json");




  if($responseData["status"] == "200"){
       $engine->put_session("cartid",$responseData['message']["tid"]);
        echo "ok@@".json_encode($responseData['message']); exit;
    }else{
      echo "bad@@".$responseData["message"]; exit;  
    }

}




if(isset($_REQUEST['paymentmethod'])){
    
    $paymentmethod = urldecode($_REQUEST['paymentmethod']);
$engine->db_query("UPDATE quickpay_transaction_log SET payment_method = ? WHERE transactionid = ?",array($paymentmethod,$engine->get_session("cartid")));
            
    echo "ok@".$engine->get_session("cartid"); exit;         
    }

?>
<script type="text/javascript">
var select;
function load_amount(){
   var service = $("#cabletv").val(); 
   $("#selectdrop").show();
$("#amount").html("");
$.ajax({
    type: "POST",
    url: "<?php echo $engine->config("website_root")."api/pro/tv/available_bounquet.json";?>",
    data: "service="+service,
    cache: false,
    success: function (html){
        
        if (typeof html['status'] === "undefined") {
           load_amount();
            }else{
        var dropdown = '';
        //var items = html['message']['items'];
       // var i = 0;
        //for(i=0; i<items.length; i++){
        //   dropdown += '<option  value="'+items[i]['code']+'">'+items[i]['name']+' @ '+items[i]['price']+'</option>';
        //}
        
        jQuery.each(html['message'], function(index, item) {
 dropdown += '<option  value="'+index+'">'+item+'</option>';
});
        
        


$("#amount").html(dropdown);
$("#selectdrop").hide();

}




    } });  
}
</script>

<style type="text/css">
.selectize-input{
  height:54px;
  padding-top:20px;
}
.selectize-control{float:left; border-right:1px solid #CCCCCC;}
</style>
<?php
if(isset($_REQUEST['key'])){
    $key = trim($engine->safe_html($_REQUEST['key']));
    if(!empty($key)){
    ?>
<script type="text/javascript">
jQuery(document).ready(function($){
if ( $("#cabletv option[value='<?php  echo $engine->safe_html($_REQUEST['key']);?>']").val() !== undefined) { 
$("#cabletv").val("<?php  echo $engine->safe_html($_REQUEST['key']);?>");
load_amount();
}
});
</script>
<?php }}?>



<script type="text/javascript">
var amount = 0;
function sendservice(){
    
var cabletv = $("#cabletv").val();
var cabletvtext = $("#amount option:selected").text();
var meter = $("#meter").val();
var phone = $("#phone").val();
var code = $("#amount").val();

if(empty(cabletv) || empty(meter) || empty(code) || empty(phone)){
  $.alert("All fields marked asteric are compulsory"); return false; 
}

   if(phone.length < 11 || phone.length > 11){
      $.alert("Invalid Phone Number (11 Digits Required"); return false; 
    }
    
  var tpm = cabletvtext.split('@');
 amount = parseInt(tpm[1]);   
    
$("#sendcabletv").html('<i class="fa fa-spinner fa-spin"></i> Loading');    
    
var datatosend = "cabletv="+encodeURIComponent(cabletv)+"&meter="+encodeURIComponent(meter)+"&amount="+encodeURIComponent(cabletvtext)+"&code="+code+"&phone="+encodeURIComponent(phone)+"&preview=preview";
  // console.log(datatosend);
$.ajax({
type: "POST",
url: "theme/classic/pages/call/tv.php",
data: datatosend,
cache: false,
success: function(html){
 
var myreturn = html.split('@@');
    
if(myreturn[0].trim() == "ok"){
    //thirdPartyCode
var jsonObj = myreturn[1];
var obj = $.parseJSON(jsonObj);

    var res1 = obj["name"].replace("/", "_");
   // var res2 = obj["number"].replace("/", "_");
window.location.href = "/confirmation&detail1="+encodeURIComponent(res1)+"&detail2="+encodeURIComponent(obj["number"]);
$("#sendcabletv").html('Continue');  

  }else{
    $.alert(myreturn[1]);
    $("#sendcabletv").html('Continue');  
  }
  
  
   
},
error:function(ed){
   $("#sendcabletv").html('Continue');  
}
});
    




}





</script>




<div id="main1" style="padding: 20px;" class="profilebg">



<div style="font-size: 140%; margin-bottom:10px;">CABLE/TV VENDING</div>
<div style="overflow: hidden;"></div>







<style type="text/css">
.inputholder{float:none; width:100%;}
.inputholder2{margin-right:0%; }
</style>



<div style="overflow: hidden;">

<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<select onchange="load_amount()" id="cabletv" class="input" style="padding:20px 1%; padding-left:50px; margin:0px; float:left; width:99%;" >

<option  value="" hidden="hidden">Select Network</option>
<?php
$row = $engine->db_query("SELECT id,services_key,service_name FROM quickpay_services WHERE services_category = ?  AND status = '1' ORDER BY id",array(5)); 
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
<input id="meter" class="input" style="padding:20px 1%; padding-left:50px; margin:0px; float:left; width:99%;"  placeholder="Enter Smart Card Number"/>
</div>

</div>





<div style="overflow: hidden;">

<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px; position: relative;" >
<select id="amount" class="input" style="padding:20px 1%; padding-left:50px; margin:0px; float:left; width:99%;" >
<option  value="" hidden="hidden">Select Network</option>
</select>
<div id="selectdrop" class="fa fa-spinner fa-spin" style="display:hidden; position: absolute; right:30px; top:8px;"></div>
</div>
  
  
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input id="phone" class="input" style="padding:20px 1%; padding-left:50px; margin:0px; float:left; width:99%;" type="tel" placeholder="Enter Phone Number"/>
</div>

</div>





<div style="overflow: hidden;">

  
</div>



<div style="overflow: hidden;">

<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<button onclick="sendservice()" id="sendcabletv" class="mainbg shadow" style="color:white; cursor:pointer; border:none; padding:20px 1%; margin:0px; float:left; width:100%;">PAY</button>
</div>
  
</div>


</div>

