<?php 
include "../../../../../engine.autoloader.php";
$hasher = new PasswordHash(8, false);
$id = $_REQUEST['id'];


if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
       exit;
    }
}

$profile_creator = $engine->get_session("recharge4id"); 
$recharge4role = $engine->get_session("recharge4role"); 

   switch ($recharge4role){
	case "1":
    $row = $engine->db_query("SELECT email,name,mobile FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($id));
if(empty($row[0]['email'])){
    echo "Invalid Email5";
    exit;
}
	break;

	case "2":
$row = $engine->db_query("SELECT email,name,mobile FROM recharge4_account WHERE recharge4id = ? AND profile_agent = ? LIMIT 1",array($id,$profile_creator));
if(empty($row[0]['email'])){
    echo "Invalid Email5";
    exit;
}
	break;
    
    case "3":
$row = $engine->db_query("SELECT email,name,mobile FROM recharge4_account WHERE recharge4id = ? AND profile_creator = ? LIMIT 1",array($id,$profile_creator));
if(empty($row[0]['email'])){
    echo "Invalid Email5";
    exit;
}
	break;
    
	default :
     echo "Invalid Email5";
    exit;
};
 


$emaila = $row[0]['email'];
$namea = $row[0]['name'];
$mobilea = $row[0]['mobile'];


if(isset($_REQUEST['name'])){
    
$name = $_REQUEST['name']; 
$username = $_REQUEST['email'];   
$email = $_REQUEST['email']; 
$mobile = $_REQUEST['mobile'];  



if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid Email1";
    exit;
}

if(strlen($mobile)< 11){
    echo "Invalid Mobile2";
    exit;
}

if(empty($mobile)){
    echo "Invalid Mobile3";
    exit;
}

$row = $engine->db_query("SELECT recharge4id FROM recharge4_account WHERE email = ? LIMIT 1",array($email));
if(!empty($row[0]['recharge4id'])){
    if($row[0]['recharge4id'] != $id){
    echo "Invalid Email4_".$row[0]['recharge4id']."_".$id;
    exit;
    }
}




$engine->db_query("UPDATE recharge4_account SET name= ?, username= ?, email= ?, mobile=? WHERE recharge4id = ? LIMIT 1",array($name, $email, $email, $mobile, $id)); 


if(!empty($_REQUEST["password"])){
//$password = sha1(md5($_REQUEST["password"]) . $engine->config("user_key"));
$password = $hasher->shuzia_HashPassword($_REQUEST["password"],$engine->RandomString(4,20));
$engine->db_query("UPDATE recharge4_account SET password=? WHERE recharge4id = ? LIMIT 1",array($password, $id)); 
}

$details = $name."_".$email."_".$mobile;
$engine->db_query("INSERT INTO log_log (recharge4id,what,details) VALUES (?,?,?)",array($engine->get_session("recharge4id"),"EDIT_ACCOUNT",$details));
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
		url: "/theme/classic/pages/dashboard/pro/editprofile.php",
		data: "name="+name+"&email="+email+"&mobile="+mobile+"&password="+password+"&id=<?php echo $id;?>",
		cache: false,
		success: function(html){
		  //alert(html);
		  window.location.reload();
		}
	   });
}
</script>

<div class="whitemenu" style="padding: 10px; margin-top:-15px;">
<div>Name</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="name" value="<?php echo $namea;?>" style="padding:5px; width:100%;" /></div>

<div>Email</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="email" value="<?php echo $emaila;?>" style="padding:5px; width:100%;" /></div>


<div>Mobile</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="mobile" value="<?php echo $mobilea;?>" style="padding:5px; width:100%;" /></div>

<div>Password</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="password" value="" style="padding:5px; width:100%;" /></div>

<div style="padding: 5px; text-align:center; cursor:pointer;" onclick="savesetting();" class="mainbg">SAVE</div>

</div>


