<?php 
include "../../../../engine.autoloader.php";
$hasher = new PasswordHash(8, false);
$id = $_REQUEST['id'];

$rechargeproemail = $engine->get_session("rechargeproid"); 


if(isset($_REQUEST['password'])){
    

    
    
$password = $hasher->shuzia_HashPassword($_REQUEST["password"],$engine->RandomString(4,20));
$engine->db_query("UPDATE rechargepro_account_read SET readpassword=? WHERE readid = ? AND rechargeproid = ? LIMIT 1",array($password,$id,$rechargeproemail)); 


$details = $rechargeproemail."_PASS CHANGED FROM READ";
$engine->db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($engine->get_session("rechargeproid"),"PASSWORD CHANGED FROM READ",$details));

echo "1";exit;
}



?>


<script type="text/javascript">
function savesetting(){
    var password =  $("#password").val();
    
    	$.ajax({
		type: "POST",
		url: "theme/classic/pages/authorization/readchangepassword.php",
		data: "password="+password+"&id=<?php echo $id;?>",
		cache: false,
		success: function(html){
		  if(html.trim() == "1"){
		  window.location.reload();
          }else{
           $.alert(html);}
		}
	   });
}
</script>

<div class="whitemenu" style="padding: 10px; margin-top:-15px;">

<div>Password</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="password" value="" style="padding:5px; width:100%;" /></div>

<div style="padding: 5px; text-align:center; cursor:pointer;" onclick="savesetting();" class="mainbg">SAVE</div>

</div>


