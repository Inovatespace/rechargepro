<?php 
if(!isset($_SESSION)){
    include "../../../engine.autoloader.php";
}
$engine = new engine();
if(!$engine->get_session("quickpayid")){ echo "<meta http-equiv='refresh' content='0;url=/signin&pp=".$engine->url_origin()."'>"; exit;};


if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        echo "<div style='font-size:150%; text-align:center; margin-top:50px;'>this Account is not authorised for this action</div>"; exit;
    }
}


$row = $engine->db_query("SELECT password, transfer_activation,quickpayid,name,public_key,public_secret,ac_ballance,bank_name,bank_ac_name,bank_ac_number,call_back_url, name, companystate, companyaddress, companylga FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($engine->get_session("quickpayid")));
$quickpayid = $row[0]['quickpayid']; 
$name = $row[0]['name']; 
$public_key = $row[0]['public_key']; 
$public_secret = $row[0]['public_secret']; 
$companystate = $row[0]['companystate'];  
$companyaddress = $row[0]['companyaddress']; 
$transfer_activation = $row[0]['transfer_activation'];
$ac_ballance = $row[0]['ac_ballance'];
$bank_name = $row[0]['bank_name'];
$bank_ac_name = $row[0]['bank_ac_name'];
$bank_ac_number = $row[0]['bank_ac_number'];
$call_back_url = $row[0]['call_back_url'];
$companylga = $row[0]['companylga'];
$dbpassword = $row[0]['password'];

$check1 = 'checked="checked"';
//if($profile_process_transaction == 1){
//$check1 = '';  
//}

$check2 = 'checked="checked"';
//if($sendall_email == 0){
//$check2 = '';  
//}














if(isset($_REQUEST['password'])){
    
    if ($engine->CheckPassword($_REQUEST['password'], $dbpassword)){
        echo "Password cannot be similar to old password";
        exit;
        }
        
$hasher = new PasswordHash(8, false);
$password = $hasher->shuzia_HashPassword($_REQUEST["password"],$engine->RandomString(4,20));
$engine->db_query("UPDATE quickpay_account SET password = ? WHERE quickpayid = ? LIMIT 1",array($password,$engine->get_session("quickpayid")));

echo "1"; exit;
}

if(isset($_REQUEST['refreshkey'])){
$public_secret = $engine->RandomString(4,30);
//$engine->db_query("UPDATE quickpay_account SET public_secret=? WHERE quickpayid = ? LIMIT 1",array($public_secret,$engine->get_session("quickpayid")));
exit;    
}

if(isset($_REQUEST['bankname'])){
    if($transfer_activation == 1){exit;}
    $bankname = $_REQUEST['bankname'];
    $acname = $_REQUEST['acname'];
    $acnumber = $_REQUEST['acnumber'];
    
$engine->db_query("UPDATE quickpay_account SET bank_name=?, bank_ac_name=?, bank_ac_number=? WHERE quickpayid = ? LIMIT 1",array($bankname,$acname,$acnumber,$engine->get_session("quickpayid")));
    exit;
}




if(isset($_REQUEST['state'])){
    if($transfer_activation == 1){exit;}
    $state = $_REQUEST['state'];
    $lga = $_REQUEST['lga'];
    $name = $_REQUEST['name'];
    $address = $_REQUEST['address'];
    
$engine->db_query("UPDATE quickpay_account SET name=?, companyaddress=?, companystate=?, companylga = ? WHERE quickpayid = ? LIMIT 1",array($name,$address,$state,$lga,$engine->get_session("quickpayid")));
    exit;
}
?>
<style type="text/css">
.mbtn{margin-bottom:5px;}
</style>
<style type="text/css">
.transparent0{
    -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";/*old explorer*/
filter: alpha(opacity=0); /* internet explorer */
-khtml-opacity: 0; /* khtml, old safari */
-moz-opacity: 0; /* mozilla, netscape */
opacity: 0; /* fx, safari, opera */
}
</style>

<script>
function uploadimg(url){
    'use strict';
    $('.fileupload').fileupload({
        url: url,
        //acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 10000000, // 5 MB
        dataType: 'json',
        done: function (e, data) {
              //bannerholder
              $('#userimg').attr("src","avater/<?php echo $quickpayid;?>.jpg?id="+Math.random());
                $('#previewimg').html('<img src="avater/<?php echo $quickpayid;?>.jpg?id='+Math.random()+'"  style="width: 100%;" />');
                $("#psendholder").show();
        },
        beforeSend : function(xhr, opts){
            $("#previewimg").prepend('<div id="progress" class="progress" style="margin-bottom: 5px;"><div class="progress-bar progress-bar-success"></div></div>');
            },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).on('fileuploaddone', function (e, data) {
        $('#progress .progress-bar').css('width','0%');
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
};
</script>

<style type="text/css">
.mbtn{overflow: hidden;}
</style>

<div style="width:100%;">
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:5px 0px; overflow:hidden;">



<style type="text/css">
.cont1{float: left; width: 57%; overflow:hidden;}
.cont2{float: right; width:40%; overflow: hidden;}

@media (max-width: 800px) {
    .cont1{width: 100%;}
    .cont2 {width:100%; padding-top:5px;}
}
</style>

<div class="cont1">




<script type="text/javascript">
  jQuery(document).ready(function($){
    setlga();
  });


function setlga(){
    var state = $("#stateb").val();
    
    $("#lgaholder").html('<i class="fa fa-spinner fa-spin"></i> Loading');
    
    	$.ajax({
		type: "POST",
		url: "/secure/lga.php",
		data: "state="+state,
		cache: false,
		success: function(html) {
           $("#lgaholder").html(html);
           <?php if(!empty($companylga)){?>
           $("#lga").val("<?php echo $companylga;?>"); <?php }?>
		}
	});
    
    
    
}

function changename(){
var name = $("#name1").val(); 
var address = $("#address1").val();  
var lga = $("#lga").val();  
var state = $("#stateb").val(); 

     $(".fa-save").addClass("fa-spinner fa-spin");

        if(empty(lga)){$.alert("LGA is complusory"); return false;}
         if(empty(state)){$.alert("state is complusory"); return false;}
          if(empty(name)){$.alert("name is complusory"); return false;}
           if(empty(address)){$.alert("address is complusory"); return false;}
           
    
    	$.ajax({
		type: "POST",
		url: "/theme/clasic/pages/setting.php",
		data: "state="+state+"&lga="+lga+"&name="+name+"&address="+encodeURIComponent(address),
		cache: false,
		success: function(html) {
        $(".fa-save").removeClass("fa-spinner fa-spin");
		}
	     });
  
}
</script>

<div style="" class="">
<div class="profilebg" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;"><div style="float: left;">User Info</div></div>
<div style="padding:1%;">
<div class="mbtn"><div style="float:left; width:30%;">Name:</div> <div style="float: left; width:70%;"><input value="<?php echo $name;?>" id="name1" class="input" style="width: 97%; padding:10px 1%;"  /></div></div>
<div class="mbtn"><div style="float:left; width:30%;">Address:</div> <div style="float: left; width:70%;"><input value="<?php echo $companyaddress;?>" class="input" id="address1" style="width: 97%; padding:10px 1%;" /></div></div>
<div class="mbtn"><div style="float:left; width:30%;">State:</div> <div style="float: left; width:70%;">
<select class="input" id="stateb" style="width: 97%; padding:10px 1%;" onchange="setlga()">
<?php if(!empty($companystate)){?>
<option><?php echo $companystate;?></option><?php }?>
<option>Abuja</option>
<option>Anambra</option>
<option>Enugu</option>
<option>Akwa Ibom</option>
<option>Adamawa</option>
<option>Abia</option>
<option>Bauchi</option>
<option>Bayelsa</option>
<option>Benue</option>
<option>Borno</option>
<option>Cross River</option>
<option>Delta</option>
<option>Ebonyi</option>
<option>Edo</option>
<option>Ekiti</option>
<option>Gombe</option>
<option>Imo</option>
<option>Jigawa</option>
<option>Kaduna</option>
<option>Kano</option>
<option>Katsina</option>
<option>Kebbi</option>
<option>Kogi</option>
<option>Kwara</option>
<option>Lagos</option>
<option>Nasarawa</option>
<option>Niger</option>
<option>Ogun</option>
<option>Ondo</option>
<option>Osun</option>
<option>Oyo</option>
<option>Plateau</option>
<option>Rivers</option>
<option>Sokoto</option>
<option>Taraba</option>
<option>Yobe</option>
<option>Zamfara</option>
</select></div></div>

<div class="mbtn"><div style="float:left; width:30%;">LGA:</div> <div id="lgaholder" style="float: left; width:70%;"></div></div>



<?php if($transfer_activation == 0){?>
<div class="mbtn"><button onclick="changename()"   class="mainbg"  style="cursor:pointer; float:right; padding:5px; margin:3px; border: none;"> <span class="fas fa-save"></span> UPDATE INFO</button></div><?php };?>
</div>


<div class="profilebg" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;"><div style="float: left;">Bank Account Details</div></div>

<div style="padding:1%;">
<div class="mbtn"><div style="float:left; width:30%;">Bank Name:</div> <div style="float: left; width:70%;"><input value="<?php echo $bank_name;?>" id="bankname" class="input" style="width: 97%; padding:10px 1%;" /></div></div>
<div class="mbtn"><div style="float:left; width:30%;">Account Name:</div> <div style="float: left; width:70%;"><input value="<?php echo $bank_ac_name;?>" id="acname" class="input" style="width: 97%; padding:10px 1%;" /></div></div>
<div class="mbtn"><div style="float:left; width:30%;">Account Number:</div> <div style="float: left; width:70%;"><input value="<?php echo $bank_ac_number;?>" id="acnumber" class="input" style="width: 97%; padding:10px 1%;" /></div></div>

<?php if($transfer_activation == 0){?>
<div class="mbtn"><button   class="mainbg"  onclick="changeaccount()" style="cursor:pointer; float:right; padding:5px; margin:3px; border: none;"> <span class="fas fa-save"></span> Save Bank Details</button></div><?php }?>
</div>



</div>










<script type="text/javascript">
function changeaccount(){
    var bankname = $("#bankname").val();
    var acname = $("#acname").val();
    var acnumber = $("#acnumber").val();
  
    
	$.ajax({
		type: "POST",
		url: "/theme/clasic/pages/setting.php",
		data: "bankname="+bankname+"&acname="+acname+"&acnumber="+acnumber,
		cache: false,
		success: function(html) {
           $.alert("Bank Details Saved");
		}
	});
}
</script>
</div>



<div class="cont2" style="">

<div style="" class="">
<div class="profilebg" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;"><div style="float: left;">Service Charge</div></div>
<div style="padding:1%;">
<div class="mbtn"><div style="float:left; width:30%;">Service Charge:</div> <div style="float: left; width:70%;"><select class="input"  style="width: 97%; padding:10px 1%;">
	<option value="1">Active</option>
	<option value="0">Disabled</option>
</select></div></div>
<div class="mbtn"><div style="float:left; width:30%;">Service Amount:</div> <div style="float: left; width:70%;"><input class="input" id="yyy" type="text" style="width: 97%; padding:10px 1%;" /></div></div>


<div class="mbtn"><button class="mainbg"  style="cursor:pointer; float:right; padding:5px; margin:3px; border: none;"> <span class="fas fa-save"></span> Save Changes</button></div>
</div></div>




<script type="text/javascript">


function changepassword(){
    var password1 = $("#password1").val();
    var password2 = $("#password2").val();
    
    if(empty(password1)){
       $.alert("Invalid Password"); return false; 
    }
    
    if(password1 != password2){
        $.alert("Password do not match"); return false;
    }
    
	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/setting.php",
		data: "password="+password1,
		cache: false,
		success: function(html) {
		  if(html.trim() == "1"){
				  $("#password1").val("");
          $("#password2").val("");
           $.alert("Password Saved");
           
          }else{
           $.alert(html);}
		
        

		}
	});
}
</script>

<div style="" class="">
<div class="profilebg" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;"><div style="float: left;">Password</div></div>
<div style="padding:1%;">
<div class="mbtn"><div style="float:left; width:30%;">New Password:</div> <div style="float: left; width:70%;"><input id="password1" class="input" style="width: 97%; padding:10px 1%;" type="password" /></div></div>
<div class="mbtn"><div style="float:left; width:30%;">Repeat Password:</div> <div style="float: left; width:70%;"><input class="input" id="password2" type="password" style="width: 97%; padding:10px 1%;" /></div></div>


<div class="mbtn"><button onclick="changepassword()"   class="mainbg"  style="cursor:pointer; float:right; padding:5px; margin:3px; border: none;"> <span class="fas fa-save"></span> Change Password</button></div>
</div></div>



</div>





</div></div>
<div style="clear: both;"></div>