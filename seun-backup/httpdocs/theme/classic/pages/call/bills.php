<?php
require "../../../../engine.autoloader.php";

    	if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        echo "<div style='color:white; font-size:150%; text-align:center; margin-top:50px;'>this Account is not authorised for this action</div>"; exit;
    }
}

if(isset($_REQUEST['preview'])){

if(!isset($_REQUEST['amount']) || !isset($_REQUEST['phone']) || !isset($_REQUEST['primary'])){
  echo "bad@@Please enter the compulsory fields"; exit;
}
        
$amount = urldecode($_REQUEST['amount']);
$phone = urldecode($_REQUEST['phone']); 
$email = urldecode($_REQUEST['email']);
$primary = urldecode($_REQUEST['primary']);
$tertiary = urldecode($_REQUEST["tertiary"]);
$secondary = urldecode($_REQUEST["secondary"]);
$service = urldecode($_REQUEST["service"]);



$payload = array(
    "service"=>$service,
    "mobile" => $phone,
    "primary" => $primary,
    "tertiary" => $tertiary,
    "secondary" => $secondary,
    "amount" => $amount,
    "email" => $email);
            if($engine->get_session("rechargeproid")){
$row = $engine->db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($engine->get_session("rechargeproid")));  
if(!empty($row[0]['public_secret'])){
    $payload["private_key"] = $row[0]['public_secret'];
}
}
$responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/pro/bills/auth_transaction.json");

  if($responseData["status"] == "200"){
    $engine->put_session("cartid",$responseData['message']["tid"]);
        echo "ok@@".json_encode($responseData['message']); exit;
    }else{
      echo "bad@@".$responseData["message"]; exit;  
    }

}




if(isset($_REQUEST['paymentmethod'])){
$paymentmethod = urlencode($_REQUEST['paymentmethod']);
$engine->db_query("UPDATE rechargepro_transaction_log SET payment_method = ? WHERE transactionid = ?",array($paymentmethod,$engine->get_session("cartid")));
        
echo "ok@".$engine->get_session("cartid"); exit;        
}





if(isset($_REQUEST['billpara'])){
$row = $engine->db_query("SELECT bill_primary_field,service_name, bill_secondary_field, bill_tertiary_field FROM rechargepro_services WHERE services_key = ? AND status = '1'",array($_REQUEST['billpara'])); 


$fieldcount = 1;
$tertiary_type = "";
$secondary_type = "";

$bill_secondary_field_title = "";
$bill_tertiary_field_title = "";


$bill_primary_field = $row[0]['bill_primary_field'];
$service_name = $row[0]['service_name']; 
$bill_secondary_field = $row[0]['bill_secondary_field']; 
$bill_tertiary_field = $row[0]['bill_tertiary_field'];


 $primary_type = '<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" ><input  id="primary" class="input" style="padding:20px 1%; margin:0px; width:99%;  font-size: 120%;" type="text" placeholder="'.$bill_primary_field.'"/></div>'; 


if (strpos($bill_secondary_field, '@') !== false) {
    $ex = explode("@",$bill_secondary_field);
    $bill_secondary_field_title = $ex[0];
 
    
    
    if($ex[1] == "select"){
$fieldcount++;
$secondary_type = '<div class="inputholder" style="margin-bottom:10px; position: relative;" ><select  id="secondary" class="input" style="padding:20px 1%; margin:0px; width:99%; font-size: 120%;" ><option value="" hidden="hidden">'.$bill_secondary_field_title.'</option></div>';



    if (strpos($ex[2], '=') !== false) {
    $ex = explode(";",$ex[2]);
    for($i = 0; $i < count($ex); $i++){
    $exb = explode("=",$ex[$i]);
        $secondary_type .= "<option value='$exb[0]'>$exb[1]</option>";
    }
    }
     $secondary_type .= '</select></div>';
    }else{
        $fieldcount++;
       $secondary_type = '<div class="inputholder" style="margin-bottom:10px; position: relative;" ><input  id="secondary" class="input" style="padding:20px 1%; margin:0px; width:99%;  font-size: 120%;" type="text" placeholder="'.$bill_secondary_field_title.'"/></div>'; 
    }
    

    }

if (strpos($bill_tertiary_field, '@') !== false) {
     $ex = explode("@",$bill_tertiary_field);
    $bill_tertiary_field_title = $ex[0];
    
    if($ex[1] == "select"){
        $fieldcount++;
        $tertiary_type = '<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" ><select  id="tertiary" class="input" style="padding:20px 1%; margin:0px; width:99%;  font-size: 120%;" ><option value="" hidden="hidden">'.$bill_tertiary_field_title.'</option>';
        
    if (strpos($ex[2], '=') !== false) {
    $ex = explode(";",$ex[2]);
    for($i = 0; $i < count($ex); $i++){
    $exb = explode("=",$ex[$i]);
$tertiary_type .= "<option value='$exb[0]'>$exb[1]</option>";
    }
    }
    $tertiary_type .= '</select></div>';
    
    }else{
        $fieldcount++;
        $tertiary_type = '<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" ><input  id="tertiary" class="input" style="padding:20px 1%; margin:0px; width:99%;" type="text" placeholder="'.$bill_tertiary_field_title.'"/></div>';
    }
    
    
    }
    echo $fieldcount."#@#".$primary_type.$tertiary_type.$secondary_type;
    exit;
    }
    
   
?>


<style type="text/css">
.inputholder{float:left; width:48%;}
.inputholder2{margin-right:4%; }
@media only screen and (max-width: 525px) {
    /* For mobile phones: */
.inputholder{float:none; width:100%;}
.inputholder2{margin-right:0%; }
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($){
});


function callbill(){
var service = $("#pbill").val(); 

$("billholder").html('<i class="fa fa-spinner fa-spin"></i> Loading');

$.ajax({
    type: "POST",
    url: "/theme/classic/pages/call/bills.php",
    data: "billpara="+service,
    cache: false,
    success: function (html){
        var d = html.split("#@#");
        
     
   $("#amount").show();;
   $("#phone").show();
   
   
   $("#billholder").html(d[1]);
   
  
    

    } });  
}

var amount = 0;
function preview(){
var bill = $("#pbill").val();
var primary = $("#primary").val();
var amountb = $("#amount").val();
var phone = $("#phone").val();
var email = "";

var tertiary = $("#tertiary").val();
var secondary = $("#secondary").val();


amount = parseInt(amountb);
$("#sendairtime").html('<i class="fa fa-spinner fa-spin"></i> Loading');   


$.ajax({
type: "POST",
url: "/theme/classic/pages/call/bills.php",
data: "preview=preview&amount="+encodeURIComponent(amountb)+"&phone="+encodeURIComponent(phone)+"&email="+encodeURIComponent(email)+"&primary="+primary+"&tertiary="+tertiary+"&secondary="+secondary+"&service="+bill,
cache: false,
success: function(html){
        
var myreturn = html.split('@@');   
    
    
if(myreturn[0].trim() == "ok"){
    //thirdPartyCode
    var jsonObj = myreturn[1];
    var obj = $.parseJSON(jsonObj);
          
    var res1 = obj["primary"].replace("/", "_");
    var res2 = obj["name"].replace("/", "_");
     window.location.href = "/confirmation&detail1="+encodeURIComponent(res1)+"&detail2="+encodeURIComponent(res2);
  

  }else{
    $.alert(myreturn[1]);
  }
  
$("#sendairtime").html('Continue');   

}
});

}
</script>


<div id="main1" style="padding: 20px;" class="profilebg">

<div style="text-align: right; margin-top:-10px;"><a href="/home"><img src="/java/display/images/close2.png" width="24" height="24" /></a></div>

<div style="font-size: 140%;  margin-bottom:10px;">BILLS</div>
<div style="overflow: hidden;"></div>


<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<select id="pbill" onchange="callbill()" class="input" style="padding:20px 1%; margin:0px; width:99%; font-size: 120%;" >

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
</div>

<div class="inputholder" style="margin-bottom:10px; position: relative;" >
<input  id="phone" class="input" style="padding:20px 1%; margin:0px; width:99%;" type="text" placeholder="Enter Phone Number"/>
</div>

<div id="billholder" style="">&nbsp;</div>






<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<input class="input" style="padding:20px 1%; margin:0px; width:99%;" id="amount" type="text" placeholder="Enter Amount"/>
</div>

<div class="inputholder" style="margin-bottom:10px; position: relative;" >
<input id="email" class="input" style="padding:20px 1%; margin:0px; width:99%;" type="text" placeholder="Email Address not compulsory"/>
</div>
<div style="clear: both;"></div>


<div style="overflow: hidden;">
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<button type="submit" id="sendairtime" onclick="preview()" class="mainbg shadow" style="cursor:pointer; border:none; color:white; padding:20px 1%; margin:0px; float:left; width:100%;" >PAY</button></div></div>

</div>
  