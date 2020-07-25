<?php 
if(!isset($_SESSION)){
    include "../../../engine.autoloader.php";
}
$engine = new engine();





$row = $engine->db_query("SELECT password, transfer_activation,recharge4id,name,public_key,public_secret,ac_ballance,bank_name,bank_ac_name,bank_ac_number,call_back_url, name, companystate, companyaddress, companylga FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($engine->get_session("recharge4id")));
$recharge4id = $row[0]['recharge4id']; 
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
$engine->db_query("UPDATE recharge4_account SET password = ? WHERE recharge4id = ? LIMIT 1",array($password,$engine->get_session("recharge4id")));

echo "1"; exit;
}

if(isset($_REQUEST['refreshkey'])){
$public_secret = $engine->RandomString(4,30);
//$engine->db_query("UPDATE recharge4_account SET public_secret=? WHERE recharge4id = ? LIMIT 1",array($public_secret,$engine->get_session("recharge4id")));
exit;    
}

if(isset($_REQUEST['bankname'])){
    if($transfer_activation == 1){exit;}
    $bankname = $_REQUEST['bankname'];
    $acname = $_REQUEST['acname'];
    $acnumber = $_REQUEST['acnumber'];
    
$engine->db_query("UPDATE recharge4_account SET bank_name=?, bank_ac_name=?, bank_ac_number=? WHERE recharge4id = ? LIMIT 1",array($bankname,$acname,$acnumber,$engine->get_session("recharge4id")));
    exit;
}




if(isset($_REQUEST['name'])){
    if($transfer_activation == 1){exit;}
    $name = $_REQUEST['name'];
    $address = $_REQUEST['address'];
    
$engine->db_query("UPDATE recharge4_account SET name=?, companyaddress=? WHERE recharge4id = ? LIMIT 1",array($name,$address,$engine->get_session("recharge4id")));
    exit;
}
?>
<style type="text/css">
.mbtn{margin-bottom:5px;}
</style>

<style type="text/css">
.mbtn{overflow: hidden;}
</style>

<div style="width:100%; background-color: white;">
<div style="padding:5px 0px; overflow:hidden;">


<div class="cont1">




<script type="text/javascript">
function changename(){
var name = $("#name1").val(); 
var address = $("#address1").val();  

     $(".fa-save").addClass("fa-spinner fa-spin");

          if(empty(name)){$.alert("name is complusory"); return false;}
           if(empty(address)){$.alert("address is complusory"); return false;}
           
    
    	$.ajax({
		type: "POST",
		url: "/theme/clasic/pages/dashboard/setting.php",
		data: "name="+name+"&address="+encodeURIComponent(address),
		cache: false,
		success: function(html) {
        $(".fa-save").removeClass("fa-spinner fa-spin");
        $.alert("Name/Address Saved");
		}
	     });
  
}
</script>

<div style="" class="">
<div class="profilebg" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;"><div style="float: left;">User Info</div></div>
<div style="padding:1%;">
<div class="mbtn"><div style="float:left; width:30%;">Name:</div> <div style="float: left; width:70%;"><input value="<?php echo $name;?>" id="name1" class="input" style="width: 97%; padding:10px 1%;"  /></div></div>
<div class="mbtn"><div style="float:left; width:30%;">Address:</div> <div style="float: left; width:70%;"><input value="<?php echo $companyaddress;?>" class="input" id="address1" style="width: 97%; padding:10px 1%;" /></div></div>

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
		url: "/theme/clasic/pages/dashboard/setting.php",
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
		url: "/theme/classic/pages/dashboard/setting.php",
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