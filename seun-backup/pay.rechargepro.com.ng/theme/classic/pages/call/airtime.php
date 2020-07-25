<?php 
include "../../../../engine.autoloader.php";


if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        echo "<div style='color:white; font-size:150%; text-align:center; margin-top:50px;'>this Account is not authorised for this action</div>"; exit;
    }
}

if(isset($_REQUEST['preview'])){

if(!isset($_REQUEST['category']) || !isset($_REQUEST['network']) || !isset($_REQUEST['amount']) || !isset($_REQUEST['phone']) || !isset($_REQUEST['bundle']) || !isset($_REQUEST['nopin'])){
        echo "bad@@Please enter the compulsory fields"; exit;
}
    
    
    $category = urldecode($_REQUEST['category']);
    $network = urldecode($_REQUEST['network']);
    $amount = urldecode($_REQUEST['amount']); 
    $phone = urldecode($_REQUEST['phone']); 
    
    
    switch ($category){
	case "2": //airtime
    $payload = array("service"=>$network,"amount"=>$amount,"mobile"=>$phone);
    if($engine->get_session("quickpayid")){
$row = $engine->db_query("SELECT public_secret FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($engine->get_session("quickpayid")));  
if(!empty($row[0]['public_secret'])){
    $payload["private_key"] = $row[0]['public_secret'];
}
}else{
    $payload["private_key"] = "web";
}
    $responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/pro/airtime_data/auth_airtime.json");
	break;

	case "3": //data
    $bundle = urldecode($_REQUEST['bundle']);
    $payload = array("bundle"=>$bundle,"service"=>$network,"amount"=>$amount,"mobile"=>$phone);
    if($engine->get_session("quickpayid")){
$row = $engine->db_query("SELECT public_secret FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($engine->get_session("quickpayid")));  
if(!empty($row[0]['public_secret'])){
    $payload["private_key"] = $row[0]['public_secret'];
}
}else{
    $payload["private_key"] = "web";
}
    $responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/pro/airtime_data/auth_data.json");
	break;

	case "4": //pin
    $nopin = urldecode($_REQUEST['nopin']); 
    $payload = array("nopin"=>$nopin,"service"=>$network,"amount"=>$amount,"mobile"=>$phone);
    if($engine->get_session("quickpayid")){
$row = $engine->db_query("SELECT public_secret FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($engine->get_session("quickpayid")));  
if(!empty($row[0]['public_secret'])){
    $payload["private_key"] = $row[0]['public_secret'];
}
}else{
    $payload["private_key"] = "web";
}
    $responseData = $engine->file_get_b($payload, $engine->config("website_root")."api/pro/airtime_data/auth_pin.json");
	break;

	default : echo "bad@@Please enter the compulsory fields"; exit;
    }
    
    
     

  if($responseData["status"] == "200"){
    $engine->put_session("cartid",$responseData['message']["tid"]);
        echo "ok@@".json_encode($responseData['message']); exit;
    }else{
      echo "bad@@".$responseData["message"]; exit;  
    }

}



if(isset($_REQUEST['dropdown'])){
    
    $vardrop = "";
$row = $engine->db_query("SELECT id,services_key,service_name FROM quickpay_services WHERE services_category = ? AND status = '1'",array($_REQUEST['dropdown'])); 
    for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id'];
    $services_key = $row[$dbc]['services_key'];
    $service_name = $row[$dbc]['service_name'];
    $vardrop .= "<option value='$services_key'>$service_name</option>";
        }
        
        echo $vardrop; exit;
   
    }
?>


<script type="text/javascript">
function call_network(){
var cat = $("#agentairtime").val();
$(".selectdrop").show();

$.ajax({
type: "POST",
url: "/theme/classic/pages/call/airtime.php",
data: "dropdown="+cat,
cache: false,
success: function(html){
$(".selectdrop").hide();

$("#network").html(html);

<?php if(isset($_REQUEST['key'])){?>
if ( $("#network option[value='<?php  echo $engine->safe_html($_REQUEST['key']);?>']").val() !== undefined) { 
$("#network").val("<?php echo $engine->safe_html($_REQUEST['key']);?>");  
}
  
<?php } ?>

//network
$("#amount").hide();
$("#bundle").hide();
$("#nopinsholder").hide();

if(cat == "2"){$("#amount").show(); return false;}//vtu

if(cat == "3"){change_data("3"); }//data      

}
});

}



function change_data(dvalue = ""){
    
   

    //network
    $("#amount").hide();
    $("#bundle").hide();
    $("#nopinsholder").hide();
    
    
    if(dvalue == ""){
    dvalue = $("#agentairtime").val();
    }
    
    
    if(dvalue == "2" ){$("#amount").show();}//vtu
    
    //if(dvalue == "4"){$("#amount").show();$("#nopinsholder").show();}//pin
    
    
    if(dvalue != "3"){
      return false;
    }
    
    $(".selectdrop").show();
    
    if(dvalue == "3"){
    var network = $("#network").val(); 
$.ajax({
type: "POST",
url: "<?php echo $engine->config("website_root")."api/pro/airtime_data/available_bundle.json";?>",
data: "service="+network,
cache: false,
success: function(html){
$(".selectdrop").hide();



if(html == null){
            
    $("#amount").hide();
    $("#bundle").hide();
    $("#nopinsholder").hide();
      $("#amount").show();      
            return false;
            }
            

if(typeof html['message']['bundles'] === "undefined"){
            
    $("#amount").hide();
    $("#bundle").hide();
    $("#nopinsholder").hide();
      $("#amount").show();      
            return false;
            }

                
        var dropdown = '';
        var items = html['message']['bundles'];
        var i = 0;
        for(i=0; i<items.length; i++){
            var nae = items[i]['name'];
            if(items[i]['name'] == null){
                nae = items[i]['allowance'];
            }
           dropdown += '<option  value="'+items[i]['code']+'">'+nae+' @ '+items[i]['price']+'</option>';
        }

  $("#bundle").show(); 
  $("#bundle").html(dropdown);



 }
});   
 }//data 
}




var amount = 0;
function preview(){
var agentairtime = $("#agentairtime").val();
var network = $("#network").val();
var phone = $("#phone").val();
var bundle = $("#bundle").val();
var bundletext = $("#bundle option:selected").text();


var nopins = $("#nopins").val();
amount = $("#amount").val();



if(agentairtime == "3"){
    var s = bundletext.split("@");
    amount = $.trim(s[1]);
}

$("#sendairtime").html('<i class="fa fa-spinner fa-spin"></i> Loading');   

$.ajax({
type: "POST",
url: "theme/classic/pages/call/airtime.php",
data: "preview=preview&category="+agentairtime+"&network="+encodeURIComponent(network)+"&phone="+encodeURIComponent(phone)+"&bundle="+encodeURIComponent(bundle)+"&nopin="+encodeURIComponent(nopins)+"&amount="+encodeURIComponent(amount),
cache: false,
success: function(html){
    
   // console.log(html);
        
var myreturn = html.split('@@');   
    
    
if(myreturn[0].trim() == "ok"){
    //thirdPartyCode
    var jsonObj = myreturn[1];
    var obj = $.parseJSON(jsonObj);
            
window.location.href = "/confirmation&detail1="+encodeURIComponent(obj["name"])+"&detail2="+encodeURIComponent(obj["details"]);
            

  }else{
    $.alert(myreturn[1]);
  }
  
$("#sendairtime").html('Continue');   

},error: function(xhr, status, error) {

       $.alert(status);

    }
});

amount = parseInt(amount);

}
</script>

<script type="text/javascript">
jQuery(document).ready(function($){
    call_network();
 });
</script>  
<?php
if(isset($_REQUEST['key']) && isset($_REQUEST['cat'])){
    $cat = trim($engine->safe_html($_REQUEST['cat']));
    if(!empty($cat)){
    ?>
<script type="text/javascript">
jQuery(document).ready(function($){
if ( $("#agentairtime option[value='<?php  echo $engine->safe_html($_REQUEST['cat']);?>']").val() !== undefined) { 
$("#agentairtime").val("<?php  echo $engine->safe_html($_REQUEST['cat']);?>");
call_network();
}
});
</script>
<?php }}?>

<div id="main1" style="padding: 20px;" class="profilebg">



<div style="font-size: 140%;  margin-bottom:10px;">AIRTIME</div>
<div style="overflow: hidden;"></div>



<style type="text/css">
.inputholder{float:none; width:100%;}
.inputholder2{margin-right:0%; }
</style>


<div style="">

<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<select  onchange="call_network()" id="agentairtime"  class="input" style="padding:20px 1%; padding-left:50px; margin:0px; width:99%;" >
	<option value="2">Airtime</option>
    <option value="3">Data</option>
</select>
</div>
  
  
  
<script type="text/javascript">
jQuery(document).ready(function($){

$("#sudg").css("width",$("#phone").width()-10);
});
</script>

<div class="inputholder" style="margin-bottom:10px; position: relative;" >
<input id="phone" class="input" style="padding:20px 1%; padding-left:50px; margin:0px; width:99%;" autocomplete="off" type="text" placeholder="Enter Phone/Account Number"/>

<div class="inputholder profilebg" id="sudg" style="padding:10px; top:55px; border:solid 1px #BBB9B9; position: absolute; display:none;" >
<div>Recently Credited Lines</div>
<div class="profilebg" style="font-size:85%; border:solid 1px #A29F9F; float: left; margin:3px;"><img src="/theme/classic/icons/19.jpg" width="18" style="vertical-align: middle;" /> 08183874966<span style="font-size: 85%;">[data]</span></div>
<div class="profilebg" style="font-size:85%; border:solid 1px #A29F9F; float: left; margin:3px;"><img src="/theme/classic/icons/19.jpg" width="18" style="vertical-align: middle;" /> 08183874966<span style="font-size: 85%;">[data]</span></div> 
<div class="profilebg" style="font-size:85%; border:solid 1px #A29F9F; float: left; margin:3px;"><img src="/theme/classic/icons/19.jpg" width="18" style="vertical-align: middle;" /> 08183874966<span style="font-size: 85%;">[data]</span></div>
<div class="profilebg" style="font-size:85%; border:solid 1px #A29F9F; float: left; margin:3px;"><img src="/theme/classic/icons/19.jpg" width="18" style="vertical-align: middle;" /> 08183874966<span style="font-size: 85%;">[data]</span></div>
<div class="profilebg" style="font-size:85%; border:solid 1px #A29F9F; float: left; margin:3px;"><img src="/theme/classic/icons/19.jpg" width="18" style="vertical-align: middle;" /> 08183874966<span style="font-size: 85%;">[data]</span></div><div class="profilebg" style="font-size:85%; border:solid 1px #A29F9F; float: left; margin:3px;"><img src="/theme/classic/icons/19.jpg" width="18" style="vertical-align: middle;" /> 08183874966<span style="font-size: 85%;">[data]</span></div>
<div class="profilebg" style="font-size:85%; border:solid 1px #A29F9F; float: left; margin:3px;"><img src="/theme/classic/icons/19.jpg" width="18" style="vertical-align: middle;" /> 08183874966<span style="font-size: 85%;">[data]</span></div>
<div class="profilebg" style="font-size:85%; border:solid 1px #A29F9F; float: left; margin:3px;"><img src="/theme/classic/icons/19.jpg" width="18" style="vertical-align: middle;" /> 08183874966<span style="font-size: 85%;">[data]</span></div>
<div class="profilebg" style="font-size:85%; border:solid 1px #A29F9F; float: left; margin:3px;"><img src="/theme/classic/icons/19.jpg" width="18" style="vertical-align: middle;" /> 08183874966<span style="font-size: 85%;">[data]</span></div>
<div style="clear: both;"></div>
</div>

</div>
<div style="clear: both;"></div>
</div>


<div style="overflow: hidden;">

<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px; position: relative;" >
<select onchange="change_data()" class="input" id="network" style="padding:20px 1%; padding-left:50px; margin:0px;  width:99%;" ><option value="" hidden="hidden">Select Network</option>
</select>
<div class="selectdrop fa fa-spinner fa-spin" style="display:none; position: absolute; right:30px; top:8px;"></div>
</div>



</div>



<div style="overflow: hidden;">

<div class="inputholder  inputholder2" style="overflow: hidden; margin-bottom:10px; position: relative;" >
<input id="amount" class="input" style="padding:20px 1%; padding-left:50px; margin:0px; width:99%;" type="text" placeholder="Enter Amount"/>
<select class="input" id="bundle" style="display:none; padding:20px 1%; padding-left:50px; margin:0px; width:99%;" ><option value="" hidden="hidden">Select Bunble</option>
</select>
<div class="selectdrop fa fa-spinner fa-spin" style="display:none; position: absolute; right:30px; top:8px;"></div>


</div>



  
</div>





<div style="overflow: hidden;">

<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<button type="submit" id="sendairtime" onclick="preview()" class="mainbg shadow" style="cursor:pointer; border:none; color:white; padding:20px 1%; margin:0px; float:left; width:100%;" >PAY</button>
</div>
  
</div>


</div>
