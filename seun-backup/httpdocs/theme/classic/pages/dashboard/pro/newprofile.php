<?php 
include "../../../../../engine.autoloader.php";
$hasher = new PasswordHash(8, false);
//

if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
       exit;
    }
}

if(isset($_REQUEST['name'])){
    
$name = $_REQUEST['name']; 
$username = $_REQUEST['email'];  
$password = $hasher->shuzia_HashPassword($_REQUEST["password"],$engine->RandomString(4,20));
//$password = sha1(md5($_REQUEST["password"]) . $engine->config("user_key"));
$public_key = $engine->RandomString(4,40); 
$public_secret = $engine->RandomString(4,30);  
$active = 1; 
$email = $_REQUEST['email']; 
$mobile = $_REQUEST['mobile'];  
$profile_creator = $engine->get_session("recharge4id");  
$recharge4role = $engine->get_session("recharge4role"); 

$profile_rol  = $recharge4role+1;

$row = $engine->db_query("SELECT profile_agent,recharge4_cordinator FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($profile_creator));
$profile_agent = $row[0]['profile_agent'];
$recharge4_cordinator = $row[0]['recharge4_cordinator'];


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid Email";
    exit;
}

if(strlen($mobile)< 11){
    echo "Invalid Mobile";
    exit;
}

if(empty($mobile)){
    echo "Invalid Mobile";
    exit;
}



$row = $engine->db_query("SELECT email FROM recharge4_account WHERE email = ? || mobile = ? LIMIT 1",array($email,$mobile));
if(!empty($row[0]['email'])){
    echo "Email or Mobile Exists";
    exit;
}

$row = $engine->db_query("SELECT reademail FROM recharge4_account_read WHERE reademail = ? LIMIT 1",array($email));
if(!empty($row[0]['reademail'])){
    echo "Email Exists";
    exit;
}

$profile_agent = $profile_creator;

if($recharge4role == 1){
$recharge4_cordinator = $profile_agent;   
}

$insertid = $engine->db_query("INSERT INTO recharge4_account (recharge4_cordinator,name,username,password,public_key,public_secret,active,email,mobile,profile_creator,recharge4role,profile_agent) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($recharge4_cordinator,$name, $username, $password, $public_key, $public_secret, $active, $email, $mobile, $profile_creator,$profile_rol,$profile_agent)); 




$details = $name."_".$email."_".$public_key."_".$public_secret;
$engine->db_query("INSERT INTO log_log (recharge4id,what,details) VALUES (?,?,?)",array($engine->get_session("recharge4id"),"CREAT_ACCOUNT",$details));




$row = $engine->db_query("SELECT id,services_key,cordinator_percentage,percentage,bill_formular,dateadeded,bill_recharge4full_percentage FROM recharge4_services_agent WHERE recharge4id = ?",array($engine->get_session("recharge4id")));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id']; 
    $services_key = $row[$dbc]['services_key']; 
    $percentage = $row[$dbc]['percentage']; 
    $bill_formular = $row[$dbc]['bill_formular'];  
    $dateadeded = $row[$dbc]['dateadeded'];
    $cordinator_percentage = $row[$dbc]['cordinator_percentage'];
    $bill_recharge4full_percentage = $row[$dbc]['bill_recharge4full_percentage'];
    
 
  $engine->db_query("INSERT INTO recharge4_services_agent (cordinator_percentage,percentage,bill_formular,recharge4id,services_key,bill_recharge4full_percentage) VALUES (?,?,?,?,?,?)",array($cordinator_percentage,$percentage,$bill_formular,$insertid,$services_key,$bill_recharge4full_percentage));   
    
    }
    
    
//
$row = $engine->db_query("SELECT id,services_key,fixedfee,dateadeded FROM recharge4_services_fixed WHERE recharge4id = ?",array($engine->get_session("recharge4id")));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id']; 
    $services_key = $row[$dbc]['services_key']; 
    $fixedfee = $row[$dbc]['fixedfee'];  
    $dateadeded = $row[$dbc]['dateadeded'];
    
    
 $engine->db_query("INSERT INTO recharge4_services_fixed (fixedfee,recharge4id,services_key) VALUES (?,?,?)",array($fixedfee,$insertid,$services_key));   
 
    
    }



echo "ok";
exit;
}
?>


<script type="text/javascript">
function savesetting(){
    var name =  $("#name").val();
    var email =  $("#email").val();
    var mobile =  $("#mobile").val();
    var password =  $("#password").val();
    
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/dashboard/pro/newprofile.php",
		data: "name="+name+"&email="+email+"&mobile="+mobile+"&password="+password,
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


<div>Mobile</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="mobile" value="" style="padding:5px; width:100%;" /></div>

<div>Password</div>
<div style="margin-bottom: 5px;"><input type="password" class="input" id="password" value="" style="padding:5px; width:100%;" /></div>

<div style="padding: 5px; text-align:center; cursor:pointer;" onclick="savesetting();" class="greenmenu">SAVE</div>

</div>


