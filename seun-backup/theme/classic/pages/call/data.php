<?php
 ini_set('default_charset', 'windows-1252');
include "../../../../engine.autoloader.php";
$language = $engine->getlanguage();

$countrys = $engine->country_iso();
$country = $countrys[0];
$dial_code = $countrys[4];

if (isset($_REQUEST['preview'])) {

    if (!isset($_REQUEST['network']) || !isset($_REQUEST['amount']) ||
        !isset($_REQUEST['phone']) || !isset($_REQUEST['bundle'])) {
        echo "bad@@" . $language['{%PETCF%}'];
        exit;
    }


    $network = urldecode($_REQUEST['network']);
    $amount = urldecode($_REQUEST['amount']);
    $phone = urldecode($_REQUEST['phone']);
    $email = "";

    
            $bundle = urldecode($_REQUEST['bundle']);
            $payload = array(
            "lang"=>$engine->current_language(),
            "currency"=>$engine->currency(),
                "dial_code"=>$dial_code,
                "bundle" => $bundle,
                "service" => $network,
                "amount" => $amount,
                "mobile" => $phone,
                "email" => $email);
            if ($engine->get_session("recharge4id")) {
                $row = $engine->db_query("SELECT public_secret FROM recharge4_account WHERE recharge4id = ? LIMIT 1",
                    array($engine->get_session("recharge4id")));
                if (!empty($row[0]['public_secret'])) {
                    $payload["private_key"] = $row[0]['public_secret'];
                }
            } else {
                $payload["private_key"] = "web";
            }
            $responseData = $engine->file_get_b($payload, $engine->config("website_root") .
                "api/pro/airtime_data/auth_data.json");
       


    if ($responseData["status"] == "200") {
        $engine->put_session("cartid", $responseData['message']["tid"]);
        echo "ok@@" . json_encode($responseData['message']);
        exit;
    } else {
        echo "bad@@" . $responseData["message"];
        exit;
    }

}


if (isset($_REQUEST['dropdown'])) {

    $vardrop = "";
    $row = $engine->db_query("SELECT id,services_key,service_name FROM recharge4_services WHERE services_category = ? AND country = ? AND status = '1'",
        array($_REQUEST['dropdown'],$country));
    for ($dbc = 0; $dbc < $engine->array_count($row); $dbc++) {
        $id = $row[$dbc]['id'];
        $services_key = $row[$dbc]['services_key'];
        $service_name = $row[$dbc]['service_name'];
        $vardrop .= "<option value='$services_key'>$service_name</option>";
    }

    echo $vardrop;
    exit;

}
?>


<script type="text/javascript">
function call_network(){
var cat = 3;
$(".selectdrop").show();

$.ajax({
type: "POST",
url: "/theme/classic/pages/call/data.php",
data: "dropdown="+cat,
cache: false,
success: function(html){
$(".selectdrop").hide();

$("#network").html(html);

<?php if (isset($_REQUEST['key'])) { ?>
if ( $("#network option[value='<?php echo $engine->safe_html($_REQUEST['key']); ?>']").val() !== undefined) { 
$("#network").val("<?php echo $engine->safe_html($_REQUEST['key']); ?>");  
}
  
<?php } ?>

//network
$("#amount").hide();
$("#bundle").hide();
$("#nopinsholder").hide();

change_data("3");    

}
});

}



function change_data(dvalue = ""){
    
   

    //network
    $("#amount").hide();
    $("#bundle").hide();
    $("#nopinsholder").hide();
    
    

    dvalue = "3";
    
  
    
    $(".selectdrop").show();
    
    
    var network = $("#network").val(); 
$.ajax({
type: "POST",
url: "<?php echo $engine->config("website_root") .
"api/pro/airtime_data/available_bundle.json"; ?>",
data: "service="+network+"&country=<?php echo $country;?>",
cache: false,
success: function(html){
$(".selectdrop").hide();

//console.log(html);

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
 
}




var amount = 0;
function preview(){
var agentdata = "3";
var network = $("#network").val();
var phone = $("#phone").val();
var bundle = $("#bundle").val();
var bundletext = $("#bundle option:selected").text();

var email = "";
var nopins = $("#nopins").val();
amount = $("#amount").val();



if(agentdata == "3"){
    var s = bundletext.split("@");
    amount = $.trim(s[1]);
}

$("#senddata").html('<i class="fa fa-spinner fa-spin"></i> <?php echo $language["{%LOADING%}"]; ?>');   

$.ajax({
type: "POST",
url: "theme/classic/pages/call/data.php",
data: "preview=preview&network="+encodeURIComponent(network)+"&phone="+encodeURIComponent(phone)+"&bundle="+encodeURIComponent(bundle)+"&email="+encodeURIComponent(email)+"&nopin="+encodeURIComponent(nopins)+"&amount="+encodeURIComponent(amount),
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
  
$("#senddata").html('<?php echo $language["{%CONTINUE%}"]; ?>');   

},error: function(xhr, status, error) {

       $.alert(status);

    }
});

amount = parseInt(amount);

}
</script>


<?php
if (isset($_REQUEST['key']) && isset($_REQUEST['cat'])) {
    $cat = trim($engine->safe_html($_REQUEST['cat']));
    if (!empty($cat)) {
?>
<script type="text/javascript">
jQuery(document).ready(function($){
if ( $("#agentdata option[value='<?php echo $engine->safe_html($_REQUEST['cat']); ?>']").val() !== undefined) { 
$("#agentdata").val("<?php echo $engine->safe_html($_REQUEST['cat']); ?>");
call_network("3");
}
});
</script>
<?php } else {
?>
<script type="text/javascript">
jQuery(document).ready(function($){
call_network("3");
});
</script>
<?php
    }
} else {
?>
<script type="text/javascript">
jQuery(document).ready(function($){
call_network("3");
});
</script>
<?php
} ?>

<script type="text/javascript">
jQuery(document).ready(function($){
$(':input').on('focus', function () {
  $(this).attr('autocomplete', 'off')
});
});
</script>

<div id="main1" style="padding: 20px;" class="menubody">




<div style="overflow: hidden;">
<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px; position: relative;" >
<select onchange="change_data()" class="input" id="network" style="padding:20px 1%; margin:0px;  width:100%;" ><option value="" hidden="hidden"><?php echo
$language["{%SELECT_NETWORK%}"]; ?></option>
</select>
<span class="focus-border"><i></i></span>
<div class="selectdrop fa fa-spinner fa-spin" style="display:none; position: absolute; right:30px; top:8px;"></div>
</div>
</div>



<div style="overflow: hidden;">

<div class="inputholder  inputholder2" style="overflow: hidden; margin-bottom:10px; position: relative;" >
<select class="input" id="bundle" style="padding:20px 1%; margin:0px; width:100%;" ><option value="" hidden="hidden"><?php echo
$language["{%SELECT_BUNDLE%}"]; ?></option>
</select>
<span class="focus-border"><i></i></span>
<div class="selectdrop fa fa-spinner fa-spin" style="display:none; position: absolute; right:30px; top:8px;"></div>

</div>
</div>



<div style="">
<script type="text/javascript">
jQuery(document).ready(function($){

$("#sudg").css("width",$("#phone").width()-10);
});
</script>

<div class="inputholder" style="margin-bottom:10px; position: relative;" >
<input id="phone" class="input" style="padding:20px 1%; margin:0px; width:100%;" autocomplete="off" type="text" placeholder="<?php echo
$language["{%PHONE_ACCOUNT%}"]; ?>"/>
<span class="focus-border"><i></i></span>

</div>
<div style="clear: both;"></div>
</div>





<div style="overflow: hidden;">
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<div class="container-contact100-form-btn">
<div class="wrap-contact100-form-btn">
<div class="contact100-form-bgbtn"></div>
<button class="contact100-form-btn" id="senddata" onclick="preview()">
<span> <?php echo $language["{%CONTINUE%}"]; ?> <i class="fa fa-long-arrow-right m-l-7" aria-hidden="true"></i>
</span>
</button>
</div>
</div>
</div>


  
</div>


</div>
