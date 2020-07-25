<?php 
include "../../../../engine.autoloader.php";
$hasher = new PasswordHash(8, false);
//

if(isset($_REQUEST['name'])){
    
$name = $_REQUEST['name']; 
$receiveemail = $_REQUEST['checkbox'];  
$password = $hasher->shuzia_HashPassword($_REQUEST["password"],$engine->RandomString(4,20));
$email = $_REQUEST['email'];  
$profile_creator = $engine->get_session("rechargeproid");  

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid Email";
    exit;
}



$row = $engine->db_query("SELECT email FROM rechargepro_account WHERE email = ? LIMIT 1",array($email));
if(!empty($row[0]['email'])){
    echo "Email or Mobile Exists";
    exit;
}


$row = $engine->db_query("SELECT reademail FROM rechargepro_account_read WHERE reademail = ? LIMIT 1",array($email));
if(!empty($row[0]['reademail'])){
    echo "Email Exists";
    exit;
}


$insertid = $engine->db_query("INSERT INTO rechargepro_account_read (rechargeproid,readname,reademail,receive_email,readpassword) VALUES (?,?,?,?,?)",array($profile_creator,$name,$email,$receiveemail,$password)); 




$details = $name."_".$email;
$engine->db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("rechargeproid"),"CREAT_ACCOUNT_READ",$details));



echo "ok";
exit;
}
?>


<script type="text/javascript">
function savesetting(){
    var name =  $("#name").val();
    var email =  $("#email").val();
    var password =  $("#password").val();
    
    
     var checkbox =  0;
     if ($('input.checkbox_check').is(':checked')){
        var checkbox = 1;
        }
    
    	$.ajax({
		type: "POST",
		url: "theme/classic/pages/authorization/readnewprofile.php",
		data: "name="+name+"&email="+email+"&checkbox="+checkbox+"&password="+password,
		cache: false,
		success: function(html){
		 if(html.trim() == "ok"){
		 window.location.reload();
         }else{
            $.alert(html);
         }
		}
	   });
}
</script>

<div class="whitemenu" style="padding: 10px; margin-top:-15px;">
<div>Name</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="name" value="" style="padding:5px; width:100%;" /></div>

<div>Email</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="email" value="" style="padding:5px; width:100%;" /></div>


<div>Receive End of day/Monthly Report</div>
<div style="margin-bottom: 5px;"><input type="checkbox" id="checkbox" class="checkbox_check" /></div>

<div>Password</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="password" value="" style="padding:5px; width:100%;" /></div>

<div style="padding: 5px; text-align:center; cursor:pointer;" onclick="savesetting();" class="greenmenu">SAVE</div>

</div>


